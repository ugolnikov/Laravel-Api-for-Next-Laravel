<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Product::where('is_published', true);

    //     if ($request->has('search')) {
    //         $query->where(function ($q) use ($request) {
    //             $q->where('name', 'like', '%' . $request->search . '%')
    //               ->orWhereHas('seller', function ($q) use ($request) {
    //                   $q->where('name', 'like', '%' . $request->search . '%');
    //               });
    //         });
    //     }

    //     $products = $query->paginate(5);
    //     return response()->json($products);
    // }
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('seller_id')) {
        $query->where('seller_id', $request->seller_id);
        }

        if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(12);

        return response()->json($products);
    }


    public function show(Product $product)
    {
        if (!$product->is_published) {
            abort(404);
        }

        return response()->json($product);
    }
}
