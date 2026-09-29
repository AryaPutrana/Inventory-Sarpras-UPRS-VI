<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = Item::query()
            ->when($search, function ($query, $search) {
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
            'unit_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'required|max:50',
            'description' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/items', $filename);
            $validated['photo'] = $filename;
        }

        Item::create($validated);

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
            'unit_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'required|max:50',
            'description' => 'nullable|string',
            'add_stock' => 'nullable|integer|min:0',
        ]);

        // Handle add_stock: tambah stok jika ada input
        if (!empty($validated['add_stock']) && $validated['add_stock'] > 0) {
            $validated['stock'] = $item->stock + $validated['add_stock'];
        }
        
        // Remove add_stock dari data yang akan disimpan
        unset($validated['add_stock']);

        // Handle file upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($item->photo) {
                Storage::delete('public/items/' . $item->photo);
            }
            
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/items', $filename);
            $validated['photo'] = $filename;
        }

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);
        
        // Delete photo
        if ($item->photo) {
            Storage::delete('public/items/' . $item->photo);
        }
        
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
