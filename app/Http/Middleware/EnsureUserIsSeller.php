<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Seller;

class EnsureUserIsSeller
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user instanceof Seller) {
            return $next($request);
        }

        return response()->json(['message' => 'Доступ запрещен. Требуются права продавца.'], 403);
    }
} 