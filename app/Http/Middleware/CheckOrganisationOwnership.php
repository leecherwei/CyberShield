<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganisationOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $requestedOrgId = $request->route('organisation');
        if (auth()->user()->organisation_id != $requestedOrgId && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to organisation profile.');
        }
        return $next($request);
    }
}
