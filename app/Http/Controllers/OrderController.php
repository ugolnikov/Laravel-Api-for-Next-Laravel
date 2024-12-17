<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        // Валидация входящих данных
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|regex:/^\+7\d{10}$/',
            'address' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // Группируем товары по продавцам
                $itemsBySeller = collect($request->items)->map(function ($item) {
                    $product = DB::table('products')->find($item['product_id']);
                    return [
                        'seller_id' => $product->seller_id,
                        'product' => $product,
                        'quantity' => $item['quantity']
                    ];
                })->groupBy('seller_id');

                $orders = [];

                // Создаем отдельный заказ для каждого продавца
                foreach ($itemsBySeller as $sellerId => $items) {
                    // Подсчитываем общую сумму заказа для этого продавца
                    $totalAmount = $items->sum(function ($item) {
                        return $item['product']->price * $item['quantity'];
                    });

                    // Создаем заказ
                    $order = Order::create([
                        'user_id' => auth()->id(),
                        'seller_id' => $sellerId,
                        'order_number' => 'ORD-' . Str::upper(Str::random(10)),
                        'total_amount' => $totalAmount,
                        'status' => 'pending',
                        'address' => $validated['address'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'],
                        'full_name' => $validated['fullName'],
                    ]);

                    // Создаем элементы заказа
                    foreach ($items as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item['product']->id,
                            'seller_id' => $sellerId, // Добавляем seller_id
                            'quantity' => $item['quantity'],
                            'price' => $item['product']->price,
                            'total' => $item['product']->price * $item['quantity'],
                        ]);
                    }

                    $orders[] = $order;
                }

                // Очищаем корзину пользователя
                DB::table('carts')->where('user_id', auth()->id())->delete();

                return response()->json([
                    'message' => 'Заказы успешно созданы',
                    'orders' => $orders
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при создании заказа',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // Получение всех заказов пользователя
    public function index()
    {
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)->get();

        return response()->json($orders);
    }

    // Обновление статуса заказа


    // Получение конкретного заказа
    public function show($orderNumber)
    {
        $order = Order::with(['items.product' => function ($query) {
            $query->select('id', 'name', 'price', 'images', 'image_preview');
        }])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $orderData = [
            'order_number' => $order->order_number,
            'status' => $order->status,
            'created_at' => $order->created_at,
            'total_amount' => $order->total_amount,
            'full_name' => $order->full_name,
            'email' => $order->email,
            'phone' => $order->phone,
            'address' => $order->address,
            'items' => collect($order->items)->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'is_send' => $item->is_send,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                        'images' => $item->product->images,
                        'image_preview' => $item->product->image_preview
                    ]
                ];
            })->values()->all()
        ];

        return response()->json($orderData);
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
    public function statusChange(Request $request)
    {
        $orderNumber = $request->orderNumber;
        $status = $request->status;
        $order = Order::with(['items.product' => function ($query) {
            $query->select('id', 'name', 'price', 'images');
        }])
            ->where('order_number', $orderNumber)
            ->firstOrFail();
        if ($order->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $order->status = $status;
        $order->save();

        return response()->json(['message' => 'Заказ успешно доставлен'], 200);
    }
}
