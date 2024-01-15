<?php

namespace Modules\Employees\Repositories;

use Modules\Employees\Models\EmployeeTransfer;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class EmployeeTransferRepositoryEloquent extends BaseRepository implements EmployeeTransferRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return EmployeeTransfer::class;
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
