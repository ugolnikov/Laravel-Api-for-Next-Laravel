<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // Создание заказа
    public function create(Request $request)
    {
        $validated = $request->validate([
            'total_amount' => 'required|numeric',
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'total_amount' => $validated['total_amount'],
            'status' => 'pending',
        ]);

        return response()->json($order, 201);
    }

    // Получение всех заказов пользователя
    public function index()
    {
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)->get();

        return response()->json($orders);
    }

    // Обновление статуса заказа
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);

        if ($order->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->status = $validated['status'];
        $order->save();

        return response()->json($order);
    }

    // Получение конкретного заказа
    public function show($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($order);
    }

    // Получить продавца по заказу
    public function getSellerByOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        $seller = $order->seller;

        return response()->json($seller);
    }

    // Получить все заказы продавца
    public function getOrdersBySeller($sellerId)
    {
        $seller = Seller::findOrFail($sellerId);
        $orders = $seller->orders;

        return response()->json($orders);
    }
}
