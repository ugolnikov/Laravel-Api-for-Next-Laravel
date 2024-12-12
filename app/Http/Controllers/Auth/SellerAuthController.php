<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SellerAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:200',
            'inn' => 'required|string|size:12|unique:sellers',
            'address' => 'required|string|min:20',
            'phone' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpg,jpeg,webp|max:2048',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'seller',
        ]);

        $sellerData = [
            'company_name' => $validated['company_name'],
            'inn' => $validated['inn'],
            'address' => $validated['address'],
            'phone' => $validated['phone'] ?? null,
            'user_id' => $user->id,
        ];

        if ($request->hasFile('logo')) {
            $sellerData['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $seller = Seller::create($sellerData);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('seller')
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
                    ->where('role', 'seller')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Выход выполнен успешно']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'company_name' => 'required|string|max:200',
            'inn' => 'required|string|size:12|unique:sellers,inn,' . $user->seller->id,
            'address' => 'required|string|min:20',
            'phone' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpg,jpeg,webp|max:2048',
        ]);

        $seller = $user->seller;

        // Обновляем основные поля
        $seller->company_name = $validated['company_name'];
        $seller->inn = $validated['inn'];
        $seller->address = $validated['address'];
        $seller->phone = $validated['phone'] ?? $seller->phone;

        // Обработка загрузки нового логотипа
        if ($request->hasFile('logo')) {
            if ($seller->logo) {
                Storage::disk('public')->delete($seller->logo);
            }
            $seller->logo = $request->file('logo')->store('logos', 'public');
        }

        $seller->save();

        return response()->json([
            'message' => 'Профиль успешно обновлен',
            'user' => $user->load('seller')
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('logos', 'public'); // Store in 'storage/app/public/logos'
            Log::debug('Image:',[$logo]);
            Log::debug('Image:',[$logoPath]);
            return response()->json(['logoUrl' => Storage::url($logoPath)]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);


    }
}
