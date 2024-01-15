<?php

namespace Modules\Employees\Repositories;

use App\Models\User;
use Modules\Employees\Models\Employee;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class EmployeeRepositoryEloquent extends BaseRepository implements EmployeeRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Employee::class;
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

    public function getManagers()
    {
        return Employee::whereHas('user', function ($query) {
            $query->role(User::ADMIN);
        })->get();
    }
}
