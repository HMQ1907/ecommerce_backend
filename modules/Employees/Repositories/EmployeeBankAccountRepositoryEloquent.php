<?php

namespace Modules\Employees\Repositories;

use Modules\Employees\Models\EmployeeBankAccount;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class EmployeeBankAccountRepositoryEloquent extends BaseRepository implements EmployeeBankAccountRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return EmployeeBankAccount::class;
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
