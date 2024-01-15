<?php

namespace Modules\Payroll\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class NoAccountInException extends HttpException
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
    protected $errorCode = 'no_account';

    /**
     * An error message.
     *
     * @var string
     */
    protected $message = 'Chưa có tài khoản nợ/có.';
}
