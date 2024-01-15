<?php

namespace Modules\Auth\Exceptions;

use Exception;
use Illuminate\Http\Response;

class EmailTakenException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->view('oauth.email-taken', [], Response::HTTP_BAD_REQUEST);
    }
}
