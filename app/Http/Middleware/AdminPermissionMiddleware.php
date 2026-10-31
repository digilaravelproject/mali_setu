<?php

namespace App\Http\Middleware;

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

        $module ??= $this->moduleForRoute((string) $request->route()?->getName());
        if ($module === null) {
            return $next($request);
        }

        $member = $user?->adminTeamMember;
        if (! $member?->is_active) {
            abort(403, 'Your team account is inactive.');
        }

        $permission = $module.'.'.($this->isManagementAction($request) ? 'manage' : 'view');
        if (! $user->hasAdminPermission($permission)) {
            abort(403, 'You do not have permission to access this functionality.');
        }

        return $next($request);
    }

    private function moduleForRoute(string $routeName): ?string
    {
        $routes = [
            'heroes.' => 'homepage_heroes',
            'admin.analytics' => 'analytics',
            'admin.dashboard' => 'dashboard',
            'admin.users.' => 'users',
            'admin.bloggers.' => 'bloggers',
            'admin.blogs.access' => 'bloggers',
            'admin.blogs.' => 'blogs',
            'admin.blog-categories.' => 'blogs',
            'admin.blog-comments.' => 'blogs',
            'admin.plans.' => 'plans',
            'admin.category.' => 'categories',
            'admin.businesses.' => 'businesses',
            'admin.jobs.' => 'jobs',
            'admin.donations.' => 'donations',
            'admin.volunteers.' => 'volunteers',
            'admin.matrimony.' => 'matrimony',
            'admin.casts.' => 'casts',
            'admin.educations.' => 'education',
            'admin.payments.' => 'payments',
            'admin.reports' => 'reports',
            'admin.settings' => 'settings',
            'admin.support-numbers.' => 'settings',
            'admin.notifications.' => 'settings',
            'admin.app-versions.' => 'settings',
            'admin.system.' => 'settings',
        ];

        foreach ($routes as $prefix => $module) {
            if (str_starts_with($routeName, $prefix)) {
                return $module;
            }
        }

        return null;
    }

    private function isManagementAction(Request $request): bool
    {
        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return true;
        }

        $routeName = (string) $request->route()?->getName();
        foreach (['.create', '.edit', '.download', '.export', '.pdf', '.payload'] as $managementRoute) {
            if (str_contains($routeName, $managementRoute)) {
                return true;
            }
        }

        return false;
    }
}
