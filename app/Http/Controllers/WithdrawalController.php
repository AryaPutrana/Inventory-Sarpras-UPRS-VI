<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Item;
use App\Models\Rusun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Concerns\SanitizesQueryInput;

class WithdrawalController extends Controller
{
    use SanitizesQueryInput;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $this->scalarQuery($request, 'search');
        $startDate = $this->scalarQuery($request, 'start_date');
        $endDate = $this->scalarQuery($request, 'end_date');

        // Sanitasi filter tanggal. Input GET yang tidak valid dibuang (bukan di-redirect)
        // supaya halaman daftar withdrawal tidak terjadi redirect berulang.
        $filterWarning = null;

        $dateValidator = Validator::make(
            compact('startDate', 'endDate'),
            [
                'startDate' => 'nullable|date_format:Y-m-d',
                'endDate' => 'nullable|date_format:Y-m-d',
            ],
            [
                'startDate.date_format' => 'Format tanggal awal tidak valid. Gunakan format YYYY-MM-DD.',
                'endDate.date_format' => 'Format tanggal akhir tidak valid. Gunakan format YYYY-MM-DD.',
            ]
        );

        if ($dateValidator->fails()) {
            $filterWarning = $dateValidator->errors()->first() . ' Filter tanggal diabaikan.';
            $startDate = null;
            $endDate = null;
        } elseif ($startDate && $endDate && $endDate < $startDate) {
            $filterWarning = 'Tanggal akhir lebih kecil dari tanggal awal. Filter tanggal diabaikan.';
            $endDate = null;
        }

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

        return view('withdrawals.index', compact(
            'withdrawals',
            'search',
            'startDate',
            'endDate',
            'filterWarning'
        ));
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
            'quantity' => ['required', 'integer', 'min:1', 'max:' . Withdrawal::MAX_QUANTITY],
            'taken_at' => 'required|date|before_or_equal:now',
            'description' => 'nullable|string|max:60000',
        ], [
            'quantity.max' => 'Jumlah pengambilan maksimal 1.000.000 unit per transaksi.',
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

                // Cegah overflow kolom DECIMAL(15,2) pada subtotal.
                // Tanpa guard ini MySQL strict mode akan melempar QueryException (HTTP 500).
                if ($validated['subtotal'] > Withdrawal::MAX_SUBTOTAL) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Total nilai pengambilan melebihi batas maksimum yang bisa disimpan. '
                            . 'Kurangi jumlah atau harga satuan barang.',
                    ]);
                }

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
