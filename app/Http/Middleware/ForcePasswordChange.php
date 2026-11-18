<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Users with a temporary password (must_change_password = true) are sent to the
 * Change Password page until they set their own password.
 */
class ForcePasswordChange
{
    private const ALLOWED_ROUTES = ['password.change', 'password.update', 'logout'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password && !$request->routeIs(...self::ALLOWED_ROUTES)) {
            return redirect()->route('password.change')
                ->with('warning', 'Please change your temporary password before continuing.');
        }

        return $next($request);
    }
}
