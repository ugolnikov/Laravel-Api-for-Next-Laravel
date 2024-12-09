<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        if(Auth::guard(name: 'web')->check()){
            Auth::guard('web')->logout();
        };
        if(Auth::guard(name: 'sell')->check()){
            Auth::guard('sell')->logout();
        };
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
