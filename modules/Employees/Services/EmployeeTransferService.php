<?php

namespace Modules\Employees\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Models\Employee;
use Modules\Employees\Repositories\EmployeeRepository;
use Modules\Employees\Repositories\EmployeeTransferRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeTransferService extends BaseService
{
    public function __construct(EmployeeTransferRepository $employeeTransferRepository, EmployeeRepository $employeeRepository)
    {
        $this->employeeTransferRepository = $employeeTransferRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function getEmployeeTransfers($params)
    {
        $query = $this->employeeTransferRepository->makeModel()
            ->with(['employee', 'fromBranch', 'toBranch', 'fromDesignation', 'toDesignation']);

        $query = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->whereHas('employee', function (Builder $query) use ($q) {
                        $query->searchName($q);
                    });
                }, null, ''),
                AllowedFilter::exact('employee_id'),
            ])
            ->allowedSorts([
                'transfer_date',
                'notice_date',
                'created_at',
            ]);

        if (isset($params['paginate']) && $params['paginate'] == 'false') {
            return $query->get();
        }

        return $query->paginate();
    }

    public function createEmployeeTransfer(array $attrs)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail(data_get($attrs, 'employee_id'));

            $value = [
                'employee_id' => $employee->id,
                'from_branch_id' => $employee->branch_id,
                'to_branch_id' => data_get($attrs, 'to_branch_id'),
                'from_department_id' => $employee->department_id,
                'to_department_id' => data_get($attrs, 'to_department_id'),
                'from_designation_id' => $employee->designation_id,
                'to_designation_id' => data_get($attrs, 'to_designation_id'),
                'transfer_date' => data_get($attrs, 'transfer_date'),
                'notice_date' => data_get($attrs, 'notice_date'),
                'description' => data_get($attrs, 'description'),
                'job' => data_get($attrs, 'job'),
                'new_salary' => data_get($attrs, 'new_salary'),
                'new_position_allowance' => data_get($attrs, 'new_position_allowance'),
            ];

            $employeeTransfer = $this->employeeTransferRepository->create($value);

            DB::commit();

            return $employeeTransfer;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getEmployeeTransfer($id)
    {
        return $this->employeeTransferRepository->find($id);
    }

    public function editEmployeeTransfer($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail(data_get($attrs, 'employee_id'));

            $value = [
                'employee_id' => $employee->id,
                'from_branch_id' => $employee->branch_id,
                'to_branch_id' => data_get($attrs, 'to_branch_id'),
                'from_department_id' => $employee->department_id,
                'to_department_id' => data_get($attrs, 'to_department_id'),
                'from_designation_id' => $employee->designation_id,
                'to_designation_id' => data_get($attrs, 'to_designation_id'),
                'transfer_date' => data_get($attrs, 'transfer_date'),
                'notice_date' => data_get($attrs, 'notice_date'),
                'description' => data_get($attrs, 'description'),
                'job' => data_get($attrs, 'job'),
                'new_salary' => data_get($attrs, 'new_salary'),
            ];

            $employeeTransfer = $this->employeeTransferRepository->update($value, $id);

            DB::commit();

            return $employeeTransfer;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteEmployeeTransfer($id)
    {
        return $this->employeeTransferRepository->delete($id);
    }

    private function getEmployeeForTransfer($employeeId, $fromBranchId, $fromDepartmentId, $fromDesignationId)
    {
        $employee = $this->employeeRepository->findWhere([
            'id' => $employeeId,
            'branch_id' => $fromBranchId,
            'department_id' => $fromDepartmentId,
            'designation_id' => $fromDesignationId,
        ])->first();

        if (!$employee) {
            return throw new \Exception(__('employees::common.employee_not_found'));
        }
    }
}
