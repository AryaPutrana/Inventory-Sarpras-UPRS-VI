<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = Item::query()
            ->when(filled($search), function ($query) use ($search) {
                return $query->where('item_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('item_code', 'asc') // Urutan dari terkecil ke terbesar (BRG-001, BRG-002...)
            ->paginate(10); // 10 item per halaman

        return view('items.index', compact('items', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|unique:items,item_code|max:50',
            'name' => 'required|max:255',
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'unit_price' => 'required|numeric|min:0|max:9999999999999',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'required|max:50',
            'description' => 'nullable|string|max:60000',
        ]);

        $validated['min_stock'] = $validated['min_stock'] ?? 0;
        $validated['description'] = $validated['description'] ?? null;

        $photo = $request->file('photo');
        $filename = Str::uuid() . '.' . $photo->extension();

        try {
            // Simpan DB + file dalam satu transaksi; file dibersihkan bila simpan gagal
            DB::transaction(function () use ($validated, $photo, $filename) {
                $validated['photo'] = $filename;
                Item::create($validated);
                $photo->storeAs('public/items', $filename);
            });
        } catch (Throwable $e) {
            Storage::delete('public/items/' . $filename);
            throw $e;
        }

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Item::findOrFail($id);
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Item::findOrFail($id);
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'item_code' => 'required|max:50|unique:items,item_code,' . $id,
            'name' => 'required|max:255',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'unit_price' => 'required|numeric|min:0|max:9999999999999',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'required|max:50',
            'description' => 'nullable|string|max:60000',
            'add_stock' => 'nullable|integer|min:0',
        ]);

        $validated['min_stock'] = $validated['min_stock'] ?? 0;
        $validated['description'] = $validated['description'] ?? null;

        $oldPhoto = null;
        $newFilename = null;

        try {
            DB::transaction(function () use ($request, $item, &$validated, &$oldPhoto, &$newFilename) {
                // Tambah stok hanya dari server (anti lost-update: nilai stok tidak diterima dari klien)
                if (!empty($validated['add_stock']) && $validated['add_stock'] > 0) {
                    $item->increment('stock', $validated['add_stock']);
                }
                unset($validated['add_stock']);

                // Ganti foto bila ada upload baru
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $newFilename = Str::uuid() . '.' . $photo->extension();
                    $photo->storeAs('public/items', $newFilename);
                    $validated['photo'] = $newFilename;
                    $oldPhoto = $item->photo;
                }

                $item->update($validated);
            });
        } catch (Throwable $e) {
            if ($newFilename) {
                Storage::delete('public/items/' . $newFilename);
            }
            throw $e;
        }

        // Hapus foto lama hanya setelah transaksi berhasil, dan hanya jika tidak dipakai item lain
        if ($oldPhoto) {
            $stillUsed = Item::where('photo', $oldPhoto)
                ->where('id', '!=', $item->id)
                ->exists();
            if (!$stillUsed) {
                Storage::delete('public/items/' . $oldPhoto);
            }
        }

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);

        // Cegah penghapusan bila barang punya riwayat pengambilan (histori laporan harus terjaga)
        if ($item->withdrawals()->exists()) {
            return redirect()->back()
                ->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat pengambilan.');
        }

        // Hapus foto hanya jika tidak dipakai item lain (mis. default.jpg dipakai banyak barang)
        if ($item->photo) {
            $stillUsed = Item::where('photo', $item->photo)
                ->where('id', '!=', $item->id)
                ->exists();
            if (!$stillUsed) {
                Storage::delete('public/items/' . $item->photo);
            }
        }
        
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
