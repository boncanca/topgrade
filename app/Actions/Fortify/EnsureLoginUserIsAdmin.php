<?php

namespace App\Actions\Fortify;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class EnsureLoginUserIsAdmin
{
    /**
     * Handle the incoming request.
     *
     *
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if (! $user?->is_admin) {
            auth()->guard('web')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            throw ValidationException::withMessages([
                'auth_notice_title' => ['Administrator access required'],
                'auth_notice' => ['This account does not have administrator permissions.'],
                Fortify::username() => ['This account does not have administrator permissions.'],
            ]);
        }

        return $next($request);
    }
}
