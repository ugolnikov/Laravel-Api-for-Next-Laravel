<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Seller;

class EnsureUserIsSeller
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() instanceof Seller) {
            return $next($request);
        }

        return response()->json(['message' => 'Доступ запрещен'], 403);
    }
} 