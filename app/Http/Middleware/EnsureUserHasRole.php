<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Only let the request through when the user has one of the given roles.
     *
     * Usage in routes: ->middleware('role:admin,tandarts')
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $roleName = $request->user()?->role?->name;

        if ($roleName === null || ! in_array($roleName, $roles, true)) {
            abort(403, 'Je hebt onvoldoende rechten om deze pagina te bekijken.');
        }

        return $next($request);
    }
}
