<?php

namespace Modules\Employees\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class CreatedEmployeeTerminationException extends HttpException
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
    protected $errorCode = 'created_employee_termination';

    /**
     * An error message.
     *
     * @var string
     */
    public function __construct()
    {
        parent::__construct(__('employees::common.termination_allowance'));
    }
}
