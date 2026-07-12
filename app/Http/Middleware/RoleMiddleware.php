<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($this->hasRole($user, $role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access. Required roles: ' . implode(', ', $roles));
    }

    private function hasRole($user, $role): bool
    {
        return match($role) {
            'super_admin' => $user->isSuperAdmin(),
            'admin'       => $user->isAdmin(),
            'attache'     => $user->isAttache(),
            default       => false
        };
    }
}