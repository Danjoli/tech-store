<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Block sessions that belong to accounts deactivated after authentication.
     *
     * This complements the login-time check and prevents an existing session
     * from remaining usable when an administrator disables the account.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_active) {
            abort(403);
        }

        return $next($request);
    }
}
