<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AdminPermissionRegistry
{
    public static function groups(): array
    {
        return config('admin_permissions.groups', []);
    }

    public static function permissions(): array
    {
        $permissions = [];
        foreach (self::groups() as $groupName => $menus) {
            foreach ($menus as $module => $menu) {
                foreach ($menu['sections'] as $sectionName => $actions) {
                    foreach ($actions as $action) {
                        $permissions[$action['key']] = $action + [
                            'group' => $groupName,
                            'module' => $module,
                            'menu' => $menu['label'],
                            'section' => $sectionName,
                        ];
                    }
                }
            }
        }

        return $permissions;
    }

    public static function keys(): array
    {
        return array_keys(self::permissions());
    }

    public static function permissionForRoute(?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }

        foreach (self::permissions() as $key => $permission) {
            foreach ($permission['routes'] as $pattern) {
                if (Str::is($pattern, $routeName)) {
                    return $key;
                }
            }
        }

        return null;
    }

    public static function moduleForPermission(string $permission): string
    {
        return self::permissions()[$permission]['module'] ?? Str::before($permission, '.');
    }

    public static function isViewPermission(string $permission): bool
    {
        return (self::permissions()[$permission]['level'] ?? null) === 'view';
    }

    public static function deniedRouteRules(User $user): array
    {
        if ($user->isSuperAdmin()) {
            return [];
        }

        $rules = [];
        foreach (Route::getRoutes() as $route) {
            $permission = self::permissionForRoute($route->getName());
            if (! $permission || $user->hasAdminPermission($permission)) {
                continue;
            }

            $rules[] = [
                'uri' => '/'.ltrim($route->uri(), '/'),
                'methods' => array_values(array_diff($route->methods(), ['HEAD'])),
            ];
        }

        return $rules;
    }

    public static function expandLegacyPermissions(array $permissions): array
    {
        $expanded = array_values(array_intersect($permissions, self::keys()));
        foreach ($permissions as $permission) {
            if (! preg_match('/^([a-z_]+)\.(view|manage)$/', $permission, $matches)) {
                continue;
            }

            foreach (self::permissions() as $key => $definition) {
                if ($definition['module'] !== $matches[1]) {
                    continue;
                }
                if ($matches[2] === 'manage' || $definition['level'] === 'view') {
                    $expanded[] = $key;
                }
            }
        }

        return array_values(array_unique($expanded));
    }

    public static function withPageDependencies(array $permissions): array
    {
        $selected = array_values(array_intersect($permissions, self::keys()));
        $definitions = self::permissions();
        foreach ($selected as $key) {
            $definition = $definitions[$key];
            if ($definition['level'] === 'view') {
                continue;
            }

            $dependencyAdded = false;
            foreach ($definitions as $pageKey => $page) {
                if ($page['module'] === $definition['module']
                    && $page['section'] === $definition['section']
                    && $page['level'] === 'view') {
                    $selected[] = $pageKey;
                    $dependencyAdded = true;
                    break;
                }
            }
            if (! $dependencyAdded) {
                foreach ($definitions as $pageKey => $page) {
                    if ($page['module'] === $definition['module'] && $page['level'] === 'view') {
                        $selected[] = $pageKey;
                        break;
                    }
                }
            }
        }

        return array_values(array_unique($selected));
    }

    public static function firstAccessibleRoute(User $user): string
    {
        foreach (self::permissions() as $key => $permission) {
            if (! $user->hasAdminPermission($key)) {
                continue;
            }
            foreach ($permission['routes'] as $routeName) {
                if (str_contains($routeName, '*') || ! Route::has($routeName)) {
                    continue;
                }
                $route = Route::getRoutes()->getByName($routeName);
                if ($route && in_array('GET', $route->methods(), true)) {
                    return $routeName;
                }
            }
        }

        return 'admin.profile';
    }

    public static function menuLabelsFor(array $permissions): array
    {
        $labels = [];
        foreach (self::expandLegacyPermissions($permissions) as $key) {
            $definition = self::permissions()[$key] ?? null;
            if ($definition) {
                $labels[$definition['module']] = $definition['menu'];
            }
        }

        return array_values($labels);
    }
}
