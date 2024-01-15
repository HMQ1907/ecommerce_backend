<?php

namespace Modules\Employees\Repositories;

use Modules\Employees\Models\EmployeeAward;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class EmployeeAwardRepositoryEloquent extends BaseRepository implements EmployeeAwardRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return EmployeeAward::class;
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
