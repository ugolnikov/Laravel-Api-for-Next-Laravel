<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SellerProductController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $products = Product::where('seller_id', Auth::guard('sell')->id())
            ->orderBy('is_published', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $products
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:100',
            'price' => 'required|integer|min:1',
            'unit' => 'required|in:штука,упаковка',
            'short_description' => 'required|string|max:100',
            'full_description' => 'required|string|max:10000',
            'image_preview' => 'nullable|url',
            'images.*' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Ошибка валидации данных.',
                'messages' => $validator->errors()
            ], 400);
        }

        $product = new Product([
            'name' => $request->name,
            'price' => $request->price,
            'unit' => $request->unit,
            'short_description' => $request->short_description,
            'full_description' => $request->full_description,
        ]);
        $product->seller_id = Auth::id();

        if ($request->image_preview) {
            $product->image_preview = $request->image_preview;
        }

        if ($request->images) {
            $product->images = json_encode($request->images);
        }

        $product->save();

        return response()->json(['message' => 'Product created successfully.', 'product' => $product], 201);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        $validated = $request->validate([
            'name' => 'nullable|string|min:3|max:100',
            'price' => 'nullable|integer|min:1',
            'unit' => 'nullable|in:штука,упаковка',
            'short_description' => 'nullable|string|max:100',
            'full_description' => 'nullable|string|max:10000',
            'image_preview' => 'nullable|url',
            'images.*' => 'nullable|url',
        ]);

        $product->fill($validated);

        if ($request->image_preview) {
            $product->image_preview = $request->image_preview;
        }

        if ($request->images) {
            $product->images = json_encode($request->images);
        }

        $product->save();

        return response()->json(['message' => 'Product updated successfully.', 'product' => $product], 200);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }

    public function togglePublish(Product $product)
    {
        $this->authorize('update', $product);

        $product->is_published = !$product->is_published;
        $product->save();

        return response()->json([
            'message' => $product->is_published ? 'Товар опубликован' : 'Товар снят с публикации',
            'is_published' => $product->is_published
        ]);
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('images', 'public');
            return response()->json(['imageUrl' => Storage::url($imagePath)]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }
}
