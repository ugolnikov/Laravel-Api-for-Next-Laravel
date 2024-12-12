<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function seller(Request $request)
    {
        $user = Auth::guard(name: 'web')->user();

        if ($user->role === 'seller') {
            return response()->json(['message' => 'Вы уже являетесь продавцом.'], 400);
        }


        DB::transaction(function () use ($user, $request) {
            DB::table('sellers')->insert([
                'name'         => $user->name,
                'email'        => $user->email,
                'password'     => $user->password,
                'role'         => 'seller',
                'company_name' => NULL,
                'inn'          => NULL,
                'address'      => NULL,
                'phone'        => NULL,
                'logo'         => NULL,
                'is_verify'    => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            DB::table('users')->where('id', $user->id)->delete();
        });

        return response()->json(['message' => 'Роль изменена на продавца.', 'role' => 'seller']);
    }

    public function customer()
    {
        $user = Auth::guard(name: 'sell')->user();

        if ($user->role === 'customer') {
            return response()->json(['message' => 'Вы уже являетесь покупателем.'], 400);
        }

        DB::transaction(function () use ($user) {
            DB::table('users')->insert([
                'name'         => $user->name,
                'email'        => $user->email,
                'password'     => $user->password,
                'role'         => 'customer',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            DB::table('sellers')->where('id', $user->id)->delete();
        });

        return response()->json(['message' => 'Роль изменена на покупателя.', 'role' => 'customer']);
    }
}
