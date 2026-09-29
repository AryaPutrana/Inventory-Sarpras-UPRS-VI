<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Item;
use App\Models\Rusun;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $withdrawals = Withdrawal::with(['item', 'rusun'])
            ->when($search, function($query) use ($search) {
                return $query->whereHas('item', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('item_code', 'like', "%{$search}%");
                })
                ->orWhere('taken_by', 'like', "%{$search}%");
            })
            ->orderBy('taken_at', 'desc')
            ->paginate(10);

        return view('withdrawals.index', compact('withdrawals', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::orderBy('item_code', 'asc')->get(); // Urut dari terkecil (BRG-001, BRG-002...)
        $rusuns = Rusun::orderBy('name')->get();
        
        return view('withdrawals.create', compact('items', 'rusuns'));
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
            'taken_at' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Get item
        $item = Item::findOrFail($validated['item_id']);

        // Validate stock availability (BR-10)
        if ($validated['quantity'] > $item->stock) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => "Stok tidak mencukupi. Stok tersedia hanya {$item->stock}."]);
        }

        // Calculate subtotal
        $validated['unit_price'] = $item->unit_price;
        $validated['subtotal'] = $validated['quantity'] * $item->unit_price;

        // Use transaction to ensure data consistency
        DB::transaction(function () use ($validated, $item) {
            // Create withdrawal
            Withdrawal::create($validated);

            // Reduce stock (BR-12)
            $item->decrement('stock', $validated['quantity']);
        });

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

    /**
     * Get item details for AJAX request
     */
    public function getItemDetails($id)
    {
        $item = Item::findOrFail($id);
        
        return response()->json([
            'item_code' => $item->item_code,
            'unit_price' => $item->unit_price,
            'stock' => $item->stock,
            'unit' => $item->unit,
        ]);
    }
}
