<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class ModelNotFoundException extends HttpException
{
    /**
     * An HTTP status code.
     *
     * @var int
     */
    protected $status = 404;

    /**
     * An error code.
     *
     * @var string|null
     */
    protected $errorCode = 'model_not_found';

    /**
     * An error message.
     *
     * @var string
     */
    protected $message = 'No query results.';
}
