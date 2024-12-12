<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::with(['product', 'user:id,email,role'])
            ->where('user_id', Auth::id())
            ->whereHas('user', function ($query) {
                $query->where('role', 'customer');
            })
            ->get();


        $total = 0;
        foreach ($items as $item) {
            $total += $item->getTotalPrice();
        }

        return response()->json([
            'items' => $items,
            'total' => $total
        ]);
    }

    public function addToCart(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();
        $total = $cartItems->sum(function ($item) {
            return $item->getTotalPrice();
        });

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    public function removeFromCart($cartId)
    {
        $cartItem = Cart::find($cartId);
        if ($cartItem && $cartItem->user_id === Auth::id()) {
            $cartItem->delete();
            return response()->json(['message' => 'Item removed from cart']);
        }

        return response()->json(['error' => 'Item not found or unauthorized'], 454);
    }

    public function updateQuantity(Request $request, $cartId)
    {
        $quantity = $request->input('quantity');
        $cartItem = Cart::find($cartId);

        if ($cartItem && $cartItem->user_id === Auth::id()) {
            $cartItem->quantity = $quantity;
            $cartItem->save();

            $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
            $total = $cartItems->sum(function ($item) {
                return $item->getTotalPrice();
            });

            return response()->json([
                'message' => 'Cart updated successfully',
                'cartItems' => $cartItems,
                'total' => $total,
            ]);
        }

        return response()->json(['error' => 'Item not found or unauthorized'], 404);
    }
}
