<?php

namespace Modules\Departments\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Departments\Models\Department;
use Modules\Departments\Models\StatusDepartment;
use Modules\Departments\Repositories\DepartmentRepository;
use Modules\Teams\Repositories\TeamRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DepartmentService extends BaseService
{
    protected $departmentRepository;

    protected $teamRepository;

    public function __construct(DepartmentRepository $departmentRepository, TeamRepository $teamRepository)
    {
        $this->departmentRepository = $departmentRepository;
        $this->teamRepository = $teamRepository;
    }

    public function getDepartments(array $params)
    {
        return QueryBuilder::for(Department::allData())
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    return $query->where('name', 'LIKE', "%$q%");
                }, null, ''),
                AllowedFilter::exact('branch_id'),
                AllowedFilter::callback('is_chart', function (Builder $query, $isChart) {
                    if ($isChart == 0) {
                        return $query->where('is_chart', 0);
                    }

                    return $query;
                }),
            ])
            ->allowedSorts(['created_at', 'name', 'status'])
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function createDepartment(array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->departmentRepository->create([
                'branch_id' => $attrs['branch_id'],
                'manager_id' => $attrs['manager_id'],
                'parent_id' => $attrs['parent_id'],
                'name' => $attrs['name'],
                'status' => StatusDepartment::fromString($attrs['status']),
            ]);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getDepartment($id)
    {
        return $this->departmentRepository->find($id);
    }

    public function editDepartment($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];
            if (isset($attrs['branch_id'])) {
                $values['branch_id'] = $attrs['branch_id'];
            }
            if (isset($attrs['manager_id'])) {
                $values['manager_id'] = $attrs['manager_id'];
            }
            if (isset($attrs['name'])) {
                $values['name'] = $attrs['name'];
            }
            if (isset($attrs['status'])) {
                $values['status'] = StatusDepartment::fromString($attrs['status']);
            }
            if (isset($attrs['parent_id'])) {
                $values['parent_id'] = $attrs['parent_id'];
            }

            $data = $this->departmentRepository->update($values, $id);

            if ($values['status'] == StatusDepartment::INACTIVE) {
                $department = $this->departmentRepository->find($id);
                $descendants = $department->getDescendants()->pluck('id')->toArray();
                foreach ($descendants as $descendant) {
                    $this->departmentRepository->update(['status' => StatusDepartment::INACTIVE], $descendant);
                }
            }

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteTeamInDepartment($zoneId, $teamId)
    {
        try {
            DB::beginTransaction();

            $data = $this->departmentRepository->find($zoneId);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteDepartment($id)
    {
        try {
            DB::beginTransaction();

            $this->departmentRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteDepartments(array $ids)
    {
        try {
            DB::beginTransaction();

            $this->departmentRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function buildTree(array $data, $parentId = null)
    {
        $tree = [];

        foreach ($data as $item) {
            if ($item['parent_id'] === $parentId) {
                $children = $this->buildTree($data, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }

        return $tree;
    }

    public function getOverviewChart(array $params)
    {
        return Department::query()
            // ->where('branch_id', data_get($params, 'branch_id', 1))
            ->where('status', StatusDepartment::ACTIVE)
            ->get();
    }

    public function exportGenderDepartment(array $params)
    {
        $branchId = auth()->user()->branch_id;
        $status = StatusDepartment::ACTIVE;

        return Department::query()
            ->join('employees as e', 'departments.id', '=', 'e.department_id')
            ->where('departments.branch_id', $branchId)
            ->where('departments.status', $status)
            ->groupBy('departments.id', 'departments.name')
            ->select('departments.id as department_id', 'departments.name as department_name',
                DB::raw('COUNT(CASE WHEN e.gender = "male" AND e.type = "contractor" THEN 1 END) as male_contract_count'),
                DB::raw('COUNT(CASE WHEN e.gender = "female" AND e.type = "contractor" THEN 1 END) as female_contract_count'),
                DB::raw('COUNT(CASE WHEN e.gender = "male" AND e.type = "expat" THEN 1 END) as male_expatriate_count'),
                DB::raw('COUNT(CASE WHEN e.gender = "female" AND e.type = "expat" THEN 1 END) as female_expatriate_count'),
                DB::raw('COUNT(CASE WHEN e.gender = "male" AND e.type = "staff" THEN 1 END) as male_staff_count'),
                DB::raw('COUNT(CASE WHEN e.gender = "female" AND e.type = "staff" THEN 1 END) as female_staff_count'))
            ->get();
    }
}
