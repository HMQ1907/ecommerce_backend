<?php

namespace Modules\Employees\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface EmployeeRepository extends RepositoryInterface
{
    public function getManagers();
}
