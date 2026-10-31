<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Authentication required',
            ], 401);
        }

        $userRole = $request->user()->user_type;

        // Check if user has any of the required roles
        $isAdminTeamMember = in_array('admin', $roles, true)
            && $request->user()->isAdminTeamMember();

        if (! in_array($userRole, $roles) && ! $isAdminTeamMember) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden - Insufficient permissions',
                'required_roles' => $roles,
                'user_role' => $userRole,
            ], 403);
        }

        return $next($request);
    }
}
