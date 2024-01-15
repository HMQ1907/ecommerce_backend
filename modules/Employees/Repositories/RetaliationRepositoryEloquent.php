<?php

namespace Modules\Employees\Repositories;

use Modules\Employees\Models\Retaliation;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class RetaliationRepositoryEloquent extends BaseRepository implements RetaliationRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Retaliation::class;
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
