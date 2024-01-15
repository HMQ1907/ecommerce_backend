<?php

namespace Modules\Payroll\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Services\EmployeeService;
use Modules\Payroll\Models\SalaryComponent;
use Modules\Payroll\Repositories\SalaryComponentRepository;
use Modules\Users\Services\UserService;
use Spatie\QueryBuilder\QueryBuilder;

class SalaryComponentService extends BaseService
{
    protected $salaryComponentRepository;

    protected $userService;

    protected $employeeService;

    public function __construct(
        SalaryComponentRepository $salaryComponentRepository,
        UserService $userService,
        EmployeeService $employeeService)
    {
        $this->salaryComponentRepository = $salaryComponentRepository;
        $this->userService = $userService;
        $this->employeeService = $employeeService;
    }

    public function getSalaryComponents(array $params)
    {
        return QueryBuilder::for(SalaryComponent::class)
            ->where(function (Builder $query) use ($params) {
                $type = data_get($params, 'type');
                if ($type == 'employee') {
                    return $query->where('value_type', 'variable');
                } elseif ($type == 'driver') {
                    return $query->where('value_type', '<>', 'variable');
                }
            })
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getSalaryComponent($id)
    {
        return $this->salaryComponentRepository->find($id);
    }

    public function createSalaryComponent(array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->salaryComponentRepository->create([
                'name' => $attrs['name'],
                'type' => $attrs['type'],
                'value' => $attrs['value'],
                'value_type' => $attrs['value_type'],
            ]);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editSalaryComponent($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];
            if (isset($attrs['name'])) {
                $values['name'] = $attrs['name'];
            }
            if (isset($attrs['type'])) {
                $values['type'] = $attrs['type'];
            }
            if (isset($attrs['value'])) {
                $values['value'] = $attrs['value'];
            }
            if (isset($attrs['value_type'])) {
                $values['value_type'] = $attrs['value_type'];
            }

            $data = $this->salaryComponentRepository->update($values, $id);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteSalaryComponent($id)
    {
        try {
            DB::beginTransaction();

            $this->salaryComponentRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteSalaryComponents(array $ids)
    {
        try {
            DB::beginTransaction();

            $this->salaryComponentRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getMainAllowances()
    {
        return SalaryComponent::query()
            ->where('type', SalaryComponent::EARNING_TYPE)
            ->where('value_type', 'variable')->get();
    }
}
