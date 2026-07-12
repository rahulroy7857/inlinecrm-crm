<?php

namespace App\Http\Controllers\Auth\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait LogsOutGuard
{
    protected function performGuardLogout(
        Request $request,
        string $guard,
        string $loginRoute,
        ?callable $onLogout = null
    ): RedirectResponse {
        if (Auth::guard($guard)->check()) {
            if ($onLogout) {
                $onLogout(Auth::guard($guard)->user());
            }

            Auth::guard($guard)->logout();
        }

        return redirect()->route($loginRoute);
    }
}
