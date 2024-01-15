<?php

namespace Modules\Auth\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class AccountInactiveException extends HttpException
{
    /**
     * An HTTP status code.
     *
     * @var int
     */
    protected $status = 401;

    /**
     * The error code.
     *
     * @var string|null
     */
    protected $errorCode = 'account_inactive';

    /**
     * An error message.
     *
     * @var string
     */
    protected $message = 'Tài khoản không hoạt động.';
}
