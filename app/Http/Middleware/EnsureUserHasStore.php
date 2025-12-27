<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. If user is not logged in, proceed (auth middleware handles this)
        if (!$user) {
            return $next($request);
        }

        // 2. Super Admins can see global view, so they are exempt
        if ($user->role === UserRole::SUPER_ADMIN) {
            return $next($request);
        }

        // 3. If user has a store_id, they are good
        if ($user->store_id) {
            return $next($request);
        }

        // 4. If user has NO store, they must create one.
        // Prevent redirect loop if already on stores.create or stores.store
        if ($request->routeIs('stores.create') || $request->routeIs('stores.store') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('stores.create');
    }
}
