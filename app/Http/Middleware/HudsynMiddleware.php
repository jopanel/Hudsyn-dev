<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HudsynMiddleware
{
    // You can inject dependencies via the constructor if needed.
    public function __construct()
    {
        // For example, you could inject a custom auth service here.
    }

    /**
     * Handle an incoming request.
     *
     * This middleware ensures that a user is authenticated.
     * For a headless CMS, you might prefer returning a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // If using a headless API approach, you might return JSON:
            // return response()->json(['error' => 'Unauthorized'], 401);
            
            // Or, if you want to redirect to the custom login page:
            return redirect()->route('hudsyn.login');
        }

        return $next($request);
    }
}
