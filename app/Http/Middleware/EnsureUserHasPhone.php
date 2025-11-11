<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPhone
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow through if not authenticated (other middleware will handle auth)
        if (!$user) {
            return $next($request);
        }

        // Exempt routes where the user can edit their profile or password
        $routeName = optional($request->route())->getName();
        $exemptRoutes = [
            'profile.edit',
            'profile.update',
            'password.update',
            'verification.notice',
            'verification.send',
        ];

        if (in_array($routeName, $exemptRoutes, true)) {
            return $next($request);
        }

        // Require phone number to proceed
        if (blank($user->phone)) {
            toastr()->error('Please add your phone number to continue.');
            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}