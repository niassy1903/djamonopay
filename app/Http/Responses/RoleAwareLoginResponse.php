<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class RoleAwareLoginResponse implements LoginResponse
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false]);
        }

        $role = $request->user()->role;
        $dashboardRoute = UserRole::dashboardRoute($role);
        $intendedUrl = $request->session()->get('url.intended');
        $intendedPath = $intendedUrl ? parse_url($intendedUrl, PHP_URL_PATH) : null;

        if ($intendedPath && str_starts_with($intendedPath, '/dashboard/')) {
            $intendedRequest = Request::create($intendedPath);
            $intendedRoute = collect(Route::getRoutes())->first(
                fn ($route): bool => $route->matches($intendedRequest)
            );

            if (! $intendedRoute) {
                return redirect()->route($dashboardRoute);
            }

            $roleMiddleware = collect($intendedRoute->gatherMiddleware())
                ->first(fn (string $middleware): bool => str_starts_with($middleware, 'role:'));

            if ($roleMiddleware) {
                $allowedRoles = explode(',', substr($roleMiddleware, strlen('role:')));
                if (! in_array($role, $allowedRoles, true)) {
                    return redirect()->route($dashboardRoute);
                }
            }
        }

        return redirect()->intended($dashboardRoute ? route($dashboardRoute) : Fortify::redirects('login'));
    }
}
