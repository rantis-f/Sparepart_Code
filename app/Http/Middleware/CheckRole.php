<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Pastikan user login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Cocokkan role user dengan parameter middleware
        if ($request->user()->role !== $role) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
