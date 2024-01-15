<?php

namespace Modules\Employees\Repositories;

use Modules\Employees\Models\EmployeeTerminationAllowance;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class EmployeeTerminationAllowanceRepositoryEloquent extends BaseRepository implements EmployeeTerminationAllowanceRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return EmployeeTerminationAllowance::class;
    }

    /**
     * Boot up the repository, pushing criteria
     *
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
