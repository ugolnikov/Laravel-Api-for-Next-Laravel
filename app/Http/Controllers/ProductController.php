<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('seller_id')) {
        $query->where('seller_id', $request->seller_id);
        }

        if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
        }
        $query->published();
        $products = $query->paginate(15);

        return response()->json($products);
    }


    public function show($id)
    {
        $product = Product::with('seller')
            ->where('id', $id)
            ->firstOrFail();

        if (!$product->is_published) {
            abort(404);
        }

        return response()->json([
            'data' => $product,
        ]);
    }
}
