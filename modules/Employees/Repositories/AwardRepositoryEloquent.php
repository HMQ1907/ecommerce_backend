<?php

namespace Modules\Employees\Repositories;

use Modules\Employees\Models\Award;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class AwardRepositoryEloquent extends BaseRepository implements AwardRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Award::class;
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
