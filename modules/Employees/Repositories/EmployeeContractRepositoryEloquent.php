<?php

namespace Modules\Employees\Repositories;

use Modules\Employees\Models\EmployeeContract;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class EmployeeContractRepositoryEloquent extends BaseRepository implements EmployeeContractRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return EmployeeContract::class;
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
