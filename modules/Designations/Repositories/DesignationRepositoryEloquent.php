<?php

namespace Modules\Designations\Repositories;

use Modules\Designations\Models\Designation;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class DesignationRepositoryEloquent extends BaseRepository implements DesignationRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Designation::class;
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
