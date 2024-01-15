<?php

namespace Modules\Overtimes\Repositories;

use Modules\Overtimes\Models\Overtime;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class OvertimeRepositoryEloquent extends BaseRepository implements OvertimeRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Overtime::class;
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
