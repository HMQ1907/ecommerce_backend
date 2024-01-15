<?php

namespace Modules\Designations\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class PositionEmployeeTransferException extends HttpException
{
    /**
     * An HTTP status code.
     *
     * @var int
     */
    protected $status = 400;

    /**
     * An error code.
     *
     * @var string|null
     */
    protected $errorCode = 'position_employee_transfer';

    /**
     * An error message.
     *
     * @var string
     */
    public function __construct($position)
    {
        parent::__construct(__('designations::common.delete_fail_transfer', ['designation' => $position?->name]));
    }
}
