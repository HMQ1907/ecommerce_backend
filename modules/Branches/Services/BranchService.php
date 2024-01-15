<?php

namespace Modules\Branches\Services;

use App\Services\BaseService;
use Modules\Branches\Models\Branch;
use Modules\Branches\Repositories\BranchRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BranchService extends BaseService
{
    protected $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function getBranches(array $params)
    {
        return QueryBuilder::for(Branch::class)
            ->allowedFilters([
                AllowedFilter::callback('q', function ($query, $q) {
                    return $query->where('name', 'LIKE', "%$q%");
                }),
            ])
            ->allowedSorts(['created_at', 'name'])
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }
}
