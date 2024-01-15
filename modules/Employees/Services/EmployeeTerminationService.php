<?php

namespace Modules\Employees\Services;

use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Exceptions\CreatedEmployeeTerminationException;
use Modules\Employees\Models\EmployeeTerminationAllowance;
use Modules\Employees\Repositories\EmployeeTerminationAllowanceRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeTerminationService extends BaseService
{
    protected $employeeTerminationAllowanceRepository;

    public function __construct(EmployeeTerminationAllowanceRepository $employeeTerminationAllowanceRepository)
    {
        $this->employeeTerminationAllowanceRepository = $employeeTerminationAllowanceRepository;
    }

    public function getEmployeeTerminations(array $params)
    {
        return QueryBuilder::for(EmployeeTerminationAllowance::allData())
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->whereHas('employee', function ($query) use ($q) {
                        return $query->searchName($q);
                    });
                }, null, ''),
                AllowedFilter::callback('month', function (Builder $query, $month) {
                    $query->where(function ($query) use ($month) {
                        $query->whereMonth('termination_date', Carbon::parse($month)->month)
                            ->whereYear('termination_date', Carbon::parse($month)->year);
                    });
                }),
            ])
            ->defaultSort('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function createEmployeeTermination(array $attrs)
    {
        try {
            DB::beginTransaction();

            $terminationEmployees = $this->employeeTerminationAllowanceRepository->get();

            foreach ($terminationEmployees as $terminationEmployee) {
                if ($terminationEmployee->employee_id == $attrs['employee_id']) {
                    throw new CreatedEmployeeTerminationException();
                }
            }

            $data = $this->employeeTerminationAllowanceRepository->create([
                'employee_id' => data_get($attrs, 'employee_id'),
                'subject' => data_get($attrs, 'subject'),
                'type' => data_get($attrs, 'type'),
                'notice_date' => data_get($attrs, 'notice_date'),
                'termination_date' => data_get($attrs, 'termination_date'),
                'terminated_by' => data_get($attrs, 'terminated_by'),
                'description' => data_get($attrs, 'description'),
                'remaining_vacation_days' => data_get($attrs, 'remaining_vacation_days'),
            ]);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getEmployeeTermination($id)
    {
        return $this->employeeTerminationAllowanceRepository->find($id);
    }

    public function editEmployeeTermination($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];

            if (isset($attrs['employee_id'])) {
                $values['employee_id'] = $attrs['employee_id'];
            }
            if (isset($attrs['subject'])) {
                $values['subject'] = $attrs['subject'];
            }
            if (isset($attrs['type'])) {
                $values['type'] = $attrs['type'];
            }
            if (isset($attrs['notice_date'])) {
                $values['notice_date'] = $attrs['notice_date'];
            }
            if (isset($attrs['termination_date'])) {
                $values['termination_date'] = $attrs['termination_date'];
            }
            if (isset($attrs['terminated_by'])) {
                $values['terminated_by'] = $attrs['terminated_by'];
            }
            if (isset($attrs['description'])) {
                $values['description'] = $attrs['description'];
            }
            if (isset($attrs['remaining_vacation_days'])) {
                $values['remaining_vacation_days'] = $attrs['remaining_vacation_days'];
            }

            $data = $this->employeeTerminationAllowanceRepository->update($values, $id);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteEmployeeTermination($id)
    {
        try {
            DB::beginTransaction();

            $this->employeeTerminationAllowanceRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteEmployeeTerminations(array $ids)
    {
        try {
            DB::beginTransaction();

            $this->employeeTerminationAllowanceRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
