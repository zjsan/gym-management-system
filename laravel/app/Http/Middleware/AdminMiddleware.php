<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has the admin role
        if (auth()->check() && auth()->user()->role?->slug === 'admin') {
            return $next($request);
        }

        // Redirect or abort if not an admin
        return abort(403, 'Unauthorized access.');
        }
}
