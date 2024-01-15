<?php

namespace Modules\Teams\Repositories;

use Modules\Teams\Models\Team;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class TeamRepositoryEloquent extends BaseRepository implements TeamRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Team::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
