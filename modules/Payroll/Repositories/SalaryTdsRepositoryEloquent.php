<?php

namespace Modules\Payroll\Repositories;

use App\Repositories\BaseRepository;
use Modules\Payroll\Models\SalaryTds;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class SalaryTdsRepositoryEloquent extends BaseRepository implements SalaryTDSRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SalaryTds::class;
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
