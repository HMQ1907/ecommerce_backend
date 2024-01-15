<?php

namespace Modules\Teams\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class UserAlreadyInTeamException extends HttpException
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
    protected $errorCode = 'not_enough_product';

    /**
     * An error message.
     *
     * @var string
     */
    protected $message = 'Người dùng đã thuộc nhóm khác.';
}
