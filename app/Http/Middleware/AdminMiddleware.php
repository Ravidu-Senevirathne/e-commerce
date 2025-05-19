<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $roles = $user->roles()->pluck('name')->toArray();
            $hasRole = $user->hasRole('admin');

            \Log::info('AdminMiddleware check for user: ' . $user->email, [
                'roles' => $roles,
                'hasAdminRole' => $hasRole,
                'sessionId' => session()->getId()
            ]);

            if ($hasRole) {
                \Log::info('AdminMiddleware: Access granted to admin user: ' . $user->email);
                return $next($request);
            } else {
                \Log::warning('AdminMiddleware: Access denied for non-admin user: ' . $user->email);
            }
        } else {
            \Log::warning('AdminMiddleware: Access denied for unauthenticated user');
        }

        return redirect('/')->with('error', 'You do not have admin access');
    }
}
