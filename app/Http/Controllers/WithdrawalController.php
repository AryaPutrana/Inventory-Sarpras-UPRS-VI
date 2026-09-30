<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Item;
use App\Models\Rusun;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $withdrawals = Withdrawal::with(['item', 'rusun'])
            ->when(filled($search), function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->whereHas('item', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                           ->orWhere('item_code', 'like', "%{$search}%");
                    })
                    ->orWhere('taken_by', 'like', "%{$search}%");
                });
            })
            ->when($startDate, function($query) use ($startDate) {
                return $query->whereDate('taken_at', '>=', $startDate);
            })
            ->when($endDate, function($query) use ($endDate) {
                return $query->whereDate('taken_at', '<=', $endDate);
            })
            ->orderBy('taken_at', 'desc')
            ->paginate(10)
            ->appends([
                'search' => $search,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

        return view('withdrawals.index', compact('withdrawals', 'search', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::orderBy('item_code', 'asc')->get(); // Urut dari terkecil (BRG-001, BRG-002...)
        $rusuns = Rusun::orderBy('name')->get();

        // Pre-select barang dari tautan "Pengambilan Barang" di halaman detail (?item=id)
        $selectedItemId = request()->integer('item') ?: null;

        return view('withdrawals.create', compact('items', 'rusuns', 'selectedItemId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'taken_by' => 'required|string|max:255',
            'rusun_id' => 'required|exists:rusun,id',
            'quantity' => 'required|integer|min:1',
            'taken_at' => 'required|date|before_or_equal:now',
            'description' => 'nullable|string|max:60000',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Kunci baris item agar dua request bersamaan tidak mengurangi stok dobel (race condition)
                $item = Item::whereKey($validated['item_id'])->lockForUpdate()->firstOrFail();

                // Validasi ketersediaan stok (BR-10) di dalam transaksi
                if ($validated['quantity'] > $item->stock) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stok tidak mencukupi. Stok tersedia hanya {$item->stock}.",
                    ]);
                }

                // Calculate subtotal
                $validated['unit_price'] = $item->unit_price;
                $validated['subtotal'] = $validated['quantity'] * $item->unit_price;

                // Create withdrawal
                Withdrawal::create($validated);

                // Reduce stock (BR-12)
                $item->decrement('stock', $validated['quantity']);
            });
        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        }

        return redirect()->route('withdrawals.index')
            ->with('success', 'Pengambilan barang berhasil disimpan dan stok telah dikurangi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $withdrawal = Withdrawal::with(['item', 'rusun'])->findOrFail($id);
        return view('withdrawals.show', compact('withdrawal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented based on PRD
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Not implemented based on PRD
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not implemented based on PRD
        abort(404);
    }
}
