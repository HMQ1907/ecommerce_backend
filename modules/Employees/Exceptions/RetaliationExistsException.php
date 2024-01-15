<?php

namespace Modules\Employees\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class RetaliationExistsException extends HttpException
{
    protected $status = 400;

    public function __construct($retaliationDate)
    {
        parent::__construct('Retaliation month '.$retaliationDate.' already exist!');
    }
}
