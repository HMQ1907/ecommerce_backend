<?php

namespace Modules\Payroll\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Models\Employee;
use Modules\Employees\Repositories\EmployeeRepository;
use Modules\Payroll\Models\EmployeeSalary;
use Modules\Payroll\Models\EmployeeSalaryGroup;
use Modules\Payroll\Models\SalarySlip;
use Modules\Payroll\Repositories\EmployeeSalaryRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeSalaryService extends BaseService
{
    const RETIREMENT_FUND = 0.147;

    const INSURANCE_SALARY = 0.06;

    protected $employeeSalaryRepository;

    protected $employeeRepository;

    public function __construct(
        EmployeeSalaryRepository $employeeSalaryRepository, EmployeeRepository $employeeRepository
    ) {
        $this->employeeSalaryRepository = $employeeSalaryRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function getEmployeeSalaries(array $params)
    {
        return QueryBuilder::for(Employee::class)
            // ->with(['currentSalary', 'currentSalary.variableSalaries'])
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->searchName($q);
                }, null, ''),
                AllowedFilter::callback('employee_type', function (Builder $query, $employeeType) {
                    $query->where('type', $employeeType);
                }),
                AllowedFilter::exact('id'),
            ])
            ->allowedSorts(['sort_order'])
            ->defaultSort('sort_order')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getEmployeeSalary($id, $params)
    {
        return QueryBuilder::for(EmployeeSalary::class)
            ->with(['variableSalaries'])
            ->where('employee_id', $id)
            ->allowedFilters([
                AllowedFilter::callback('from_date', function (Builder $query, $fromDate) use ($params) {
                    $toDate = Arr::get($params, 'filter.to_date', now());

                    $query->where(function ($query) use ($fromDate, $toDate) {
                        $query->whereBetween('date', [$fromDate, $toDate]);
                    });
                }),
                AllowedFilter::callback('to_date', function (Builder $query, $toDate) use ($params) {
                    $fromDate = Arr::get($params, 'filter.from_date', now());

                    $query->where(function ($query) use ($fromDate, $toDate) {
                        $query->whereBetween('date', [$fromDate, $toDate]);
                    });
                }),
                AllowedFilter::scope('salary_date_between'),
            ])
            ->allowedSorts(['created_at'])
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function createEmployeeSalary(array $attrs)
    {
        try {
            DB::beginTransaction();

            $employee = $this->employeeRepository->find($attrs['employee_id']);

            $data = EmployeeSalary::query()->firstOrCreate([
                'employee_id' => data_get($attrs, 'employee_id'),
                'currency_code' => data_get($attrs, 'currency_code', 'LAK'),
                'type' => 'initial',
            ], [
                'basic_salary' => data_get($attrs, 'basic_salary'),
                'current_basic_salary' => data_get($attrs, 'basic_salary'),
                'social_security' => $employee->type == Employee::TYPE_EXPAT || !$employee->is_insurance ? 0 : $this->calculateSocialSecurity(data_get($attrs, 'basic_salary')),
                'retirement_fund' => data_get($attrs, 'basic_salary') * self::RETIREMENT_FUND,
                'insurance_salary' => $this->calculateInsuranceSalary(data_get($attrs, 'basic_salary')),
                'date' => now(),
            ]);

            foreach ($attrs['variable_salaries'] as $item) {
                $data->variableSalaries()->create([
                    'variable_component_id' => $item['variable_component_id'],
                    'variable_value' => $item['variable_value'],
                    'current_value' => $item['variable_value'],
                    'adjustment_type' => 'initial',
                ]);
            }

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    protected function calculateQuantity($quantity, $actualQty)
    {
        $currentQty = $quantity ?? 0;

        if ($actualQty > $currentQty) {
            $adjustmentType = 'increment';
        } elseif ($actualQty < $currentQty) {
            $adjustmentType = 'decrement';
        } else {
            $adjustmentType = 'initial';
        }

        return [
            'quantity' => abs($actualQty - $currentQty),
            'adjustment_type' => $adjustmentType,
        ];
    }

    protected function calculateInsuranceSalary($basicSalary)
    {
        if ($basicSalary > 4500000) {
            return 4500000 * self::INSURANCE_SALARY;
        } else {
            return $basicSalary * self::INSURANCE_SALARY;
        }
    }

    protected function updateVariable($item, $latestVariables, $data)
    {
        $currentVariable = $latestVariables->where('variable_component_id', $item['variable_component_id'])->first();
        $variableValues = $this->calculateQuantity($currentVariable->current_value ?? 0, $item['variable_value']);

        $data->variableSalaries()->create([
            'variable_component_id' => $item['variable_component_id'],
            'variable_value' => $variableValues['quantity'],
            'current_value' => $item['variable_value'],
            'adjustment_type' => $variableValues['adjustment_type'],
        ]);
    }

    public function updateEmployeeSalary(array $attrs, $id)
    {
        try {
            DB::beginTransaction();

            $employee = $this->employeeRepository->find($id);

            $latestSalary = EmployeeSalary::query()
                ->with('variableSalaries')
                ->where('employee_id', $id)->latest()->first();

            $currentBasicSalary = $latestSalary->current_basic_salary ?? 0;
            $values = $this->calculateQuantity($currentBasicSalary, $attrs['basic_salary']);

            $data = EmployeeSalary::query()->create([
                'employee_id' => $id,
                'currency_code' => data_get($attrs, 'currency_code', 'LAK'),
                'type' => $values['adjustment_type'],
                'basic_salary' => $values['quantity'],
                'current_basic_salary' => data_get($attrs, 'basic_salary'),
                'social_security' => $employee->type == Employee::TYPE_EXPAT || !$employee->is_insurance ? 0 : $this->calculateSocialSecurity(data_get($attrs, 'basic_salary')),
                'retirement_fund' => data_get($attrs, 'basic_salary') * 0.147,
                'insurance_salary' => $this->calculateInsuranceSalary(data_get($attrs, 'basic_salary')),
                'date' => now(),
                'created_by' => app()->runningInConsole() ? 1 : auth()->user()->id,
            ]);

            $latestVariables = collect($latestSalary->variableSalaries);

            foreach ($attrs['variable_salaries'] as $item) {
                $this->updateVariable($item, $latestVariables, $data);
            }

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getEmployeeSalaryGroup($id)
    {
        return EmployeeSalaryGroup::with('salaryGroup.components')
            ->where('employee_id', $id)->first()->salaryGroup ?? null;
    }

    public function reportLaoRecord(array $params)
    {
        return (new PayrollService())->getPayslips($params);
    }

    public function getNewEmployee(array $params)
    {
        if (data_get($params, 'filter.start_date_between') == null) {
            return [];
        }

        return QueryBuilder::for(SalarySlip::allData())
            ->with([
                'employee',
                'employee.branch',
                'employee.bankAccounts',
                'employee.department',
                'employee.transfers',
                'employee.designation',
                'employee.retaliations',
            ])
            ->allowedFilters([
                AllowedFilter::callback('start_date_between', function (Builder $query, $startDateBetween) {
                    $query->whereHas('employee', function ($query) use ($startDateBetween) {
                        $date = explode(',', $startDateBetween);
                        $startDate = $date[0];
                        $endDate = $date[1];
                        $query->whereBetween('date_to_company', [$startDate, $endDate]);
                    });
                }),
            ])
            ->defaultSorts('sort_order')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }
}
