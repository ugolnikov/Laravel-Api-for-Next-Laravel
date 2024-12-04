<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerProductController extends Controller
{
    // Показываем все товары продавца
    public function index()
    {
        $products = Auth::user()->products()->paginate(10);

        return response()->json($products);
    }

    // Создание нового товара
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'price' => 'required|integer|min:1',
            'unit' => 'required|in:штука,упаковка',
            'short_description' => 'required|string|max:100',
            'full_description' => 'required|string|max:10000',
            'image_preview' => 'nullable|image|mimes:jpg,jpeg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,webp|max:2048',
        ]);

        $product = new Product($validated);
        $product->seller_id = Auth::id();

        if ($request->hasFile('image_preview')) {
            $product->image_preview = $request->file('image_preview')->store('products');
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products');
            }
            $product->images = json_encode($images);
        }

        $product->save();

        return response()->json(['message' => 'Product created successfully.', 'product' => $product], 201);
    }

    // Обновление товара
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'nullable|string|min:3|max:100',
            'price' => 'nullable|integer|min:1',
            'unit' => 'nullable|in:штука,упаковка',
            'short_description' => 'nullable|string|max:100',
            'full_description' => 'nullable|string|max:10000',
            'image_preview' => 'nullable|image|mimes:jpg,jpeg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,webp|max:2048',
        ]);

        $product->fill($validated);

        if ($request->hasFile('image_preview')) {
            $product->image_preview = $request->file('image_preview')->store('products');
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products');
            }
            $product->images = json_encode($images);
        }

        $product->save();

        return response()->json(['message' => 'Product updated successfully.', 'product' => $product], 200);
    }

    // Удаление товара
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }
}
