<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class RequirePassword extends \Illuminate\Auth\Middleware\RequirePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $redirectToRoute
     * @param  string|int|null  $passwordTimeoutSeconds
     * @return mixed
     */
    public function handle($request, Closure $next, $redirectToRoute = null, $passwordTimeoutSeconds = null)
    {
        if ($this->shouldConfirmPassword($request, $passwordTimeoutSeconds)) {
            return responder()->error('password_confirmation_required', __('passwords.confirm'))->respond(423);
        }

        return $next($request);
    }

    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $passwordTimeoutSeconds
     * @return bool
     */
    protected function shouldConfirmPassword($request, $passwordTimeoutSeconds = null)
    {
        $redis = Redis::connection();

        $auth = $redis->get('auth.'.$request->bearerToken());
        $auth = json_decode($auth);

        $confirmedAt = time() - ($auth->password_confirmed_at ?? 0);

        return $confirmedAt > ($passwordTimeoutSeconds ?? $this->passwordTimeout);
    }
}
