<?php

namespace App\Http\Middleware;

use Flugg\Responder\Exceptions\Http\UnauthenticatedException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        throw new UnauthenticatedException();
    }
}
