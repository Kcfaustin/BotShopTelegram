<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'file' => 'required|file',
            'is_active' => 'boolean',
        ]);

        $path = $request->file('file')->store('products');

        $data = $request->only(['name', 'description', 'price']);
        $data['slug'] = Str::slug($data['name']);
        $data['file_path'] = $path;
        $data['file_disk'] = config('filesystems.default');
        $data['is_active'] = $request->boolean('is_active');

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produit créé !');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'file' => 'nullable|file',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'price']);
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('file')) {
            if ($product->file_path && Storage::exists($product->file_path)) {
                Storage::delete($product->file_path);
            }
            $data['file_path'] = $request->file('file')->store('products');
            $data['file_disk'] = config('filesystems.default');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour !');
    }

    public function destroy(Product $product)
    {
        // Safe delete (keep file? no, let's delete to save space since it's digital product)
        if ($product->file_path && Storage::exists($product->file_path)) {
             try {
                Storage::delete($product->file_path);
             } catch(\Exception $e) {
                 // ignore
             }
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé !');
    }
}
