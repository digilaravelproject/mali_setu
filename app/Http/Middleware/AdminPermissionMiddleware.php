<?php

namespace App\Http\Middleware;

use App\Support\AdminPermissionRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    public function handle(Request $request, Closure $next, ?string $module = null): Response
    {
        $user = $request->user();

        if ($user?->isSuperAdmin()) {
            return $next($request);
        }

        $permission = AdminPermissionRegistry::permissionForRoute($request->route()?->getName());
        if ($permission === null) {
            return $next($request);
        }

        $member = $user?->adminTeamMember;
        if (! $member?->is_active) {
            abort(403, 'Your team account is inactive.');
        }

        if (! $user->hasAdminPermission($permission)) {
            abort(403, 'You do not have permission to access this functionality.');
        }

        return $next($request);
    }
}
