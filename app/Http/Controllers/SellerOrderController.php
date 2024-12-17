<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    public function index()
    {
        $sellerId = Auth::guard('sell')->id();

        $orderItems = OrderItem::where('seller_id', $sellerId)
            ->get();

        // Добавляем name из продукта в результат
        $orderItemsWithProductName = $orderItems->map(function ($orderItem) {
            return [
                'id' => $orderItem->id,
                'order_number' => $orderItem->order ? $orderItem->order->order_number : NULL,
                'order_id' => $orderItem->order_id,
                'price' => $orderItem->price,
                'total' => $orderItem->total,
                'product_id' => $orderItem->product_id,
                'quantity' => $orderItem->quantity,
                'address' => $orderItem->order ? $orderItem->order->address : null,
                'is_send' => $orderItem->is_send,
                'seller_id' => $orderItem->seller_id,
                'product_name' => $orderItem->product ? $orderItem->product->name : null,
            ];
        });

        return response()->json($orderItemsWithProductName);}
    public function updateStatus(Request $request, $orderItemId)
    {
        $orderItem = OrderItem::findOrFail($orderItemId);

        if ($request->has('is_send')) {
            $orderItem->is_send = $request->is_send ? 'true' : 'false';
            $orderItem->save();
            $orderItem->order->updateOrderStatus();

            return response()->json(['message' => 'Статус товара обновлен']);
        }

        return response()->json(['error' => 'Не передан статус отправки товара'], 400);
    }
}
