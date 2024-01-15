<?php

namespace Modules\Payroll\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Payroll\Models\SalaryGroup;
use Modules\Payroll\Repositories\SalaryGroupRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SalaryGroupService extends BaseService
{
    protected $salaryGroupRepository;

    public function __construct(SalaryGroupRepository $salaryGroupRepository)
    {
        $this->salaryGroupRepository = $salaryGroupRepository;
    }

    public function getSalaryGroups(array $params)
    {
        return QueryBuilder::for(SalaryGroup::class)
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    return $query->where('name', 'LIKE', "%$q%");
                }),
            ])
            ->allowedSorts(['created_at', 'name'])
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getSalaryGroup($id)
    {
        return $this->salaryGroupRepository->find($id);
    }

    public function createSalaryGroup(array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->salaryGroupRepository->create([
                'name' => $attrs['name'],
            ]);

            $data->components()->sync($attrs['salary_component_ids']);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editSalaryGroup(array $attrs, $id)
    {
        try {
            DB::beginTransaction();

            $data = $this->salaryGroupRepository->update([
                'name' => $attrs['name'],
            ], $id);

            $data->components()->sync($attrs['salary_component_ids']);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteSalaryGroup($id)
    {
        try {
            DB::beginTransaction();

            $this->salaryGroupRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteSalaryGroups(array $ids)
    {
        try {
            DB::beginTransaction();

            $this->salaryGroupRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function assign($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->salaryGroupRepository->find($id);
            $data->employees()->sync($attrs['employees']);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
