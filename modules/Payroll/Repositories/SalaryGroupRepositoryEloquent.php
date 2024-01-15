<?php

namespace Modules\Payroll\Repositories;

use Modules\Payroll\Models\SalaryGroup;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class SalaryGroupRepositoryEloquent extends BaseRepository implements SalaryGroupRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SalaryGroup::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
