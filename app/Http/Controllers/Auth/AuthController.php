<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Пробуем найти пользователя среди обычных пользователей
        $user = User::where('email', $request->email)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'role' => 'customer'
            ]);
        }

        // Если не нашли среди пользователей, ищем среди продавцов
        $seller = Seller::where('email', $request->email)->first();
        
        if ($seller && Hash::check($request->password, $seller->password)) {
            $token = $seller->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $seller,
                'role' => 'seller'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Неверные учетные данные'],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Выход выполнен успешно']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $role = $user instanceof Seller ? 'seller' : 'customer';
        
        return response()->json([
            'user' => $user,
            'role' => $role
        ]);
    }
} 