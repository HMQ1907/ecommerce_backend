<?php

namespace Modules\Attendances\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class UnCheckedInException extends HttpException
{
    /**
     * An HTTP status code.
     *
     * @var int
     */
    protected $status = 400;

    /**
     * The error code.
     *
     * @var string|null
     */
    protected $errorCode = 'un_checked_in';

    /**
     * An error message.
     *
     * @var string
     */
    protected $message = 'Bạn chưa check-in.';
}
