<?php

namespace Modules\Overtimes\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Models\Employee;
use Modules\Employees\Repositories\EmployeeRepository;
use Modules\Overtimes\Models\Overtime;
use Modules\Overtimes\Repositories\OvertimeRepository;
use Modules\Payroll\Models\SalarySlip;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OvertimeService extends BaseService
{
    protected $overtimeRepository;

    protected $employeeRepository;

    public function __construct(OvertimeRepository $overtimeRepository, EmployeeRepository $employeeRepository)
    {
        $this->overtimeRepository = $overtimeRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function getOvertimes(array $params)
    {
        return QueryBuilder::for(Overtime::allData())
            ->allowedFilters([
                AllowedFilter::callback('month', function (Builder $query, $month) {
                    $query->where(function ($query) use ($month) {
                        $query->whereMonth('overtime_date', Carbon::parse($month)->month)
                            ->whereYear('overtime_date', Carbon::parse($month)->year);
                    });
                }),
                AllowedFilter::exact('employee_id'),
            ])
            ->defaultSort('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    private function updateOvertimePayslip($amount, $hrs, $overtime, $delete = false, $rates = [])
    {
        try {
            DB::beginTransaction();

            $month = Carbon::parse($overtime->overtime_date);
            $startMonth = $month->startOfMonth()->format('Y-m-d');
            $endMonth = $month->endOfMonth()->format('Y-m-d');
            $employee = $this->employeeRepository->find($overtime->employee_id);
            $payslip = SalarySlip::query()
                ->where('employee_id', $overtime->employee_id)
                ->where('salary_from', $startMonth)
                ->where('salary_to', $endMonth)
                ->first();

            if ($payslip) {
                $salaryJson = $payslip['salary_json'];

                // convert rate
                $rateLAKtoVND = $salaryJson['rate_lak_to_vnd'];
                $rateLAKtoUSD = $salaryJson['rate_lak_to_usd'];
                $rateUSDtoLAK = $salaryJson['rate_usd_to_lak'];
                foreach ($rates as $rate) {
                    if ($rate['to_currency_code'] == 'VND') {
                        $rateLAKtoVND = $rate['rate'];
                    } else {
                        $rateUSDtoLAK = $rate['rate'];
                        $rateLAKtoUSD = 1 / $rate['rate'];
                    }
                }

                //handle amount ot
                if ($delete) {
                    $realSalary = $payslip['gross_salary'] - $amount;
                    $salaryJson['amount_ot'] = $salaryJson['amount_ot'] - $amount;
                    $salaryJson['hrs'] = $salaryJson['hrs'] - $hrs;
                } else {
                    $realSalary = $amount != $salaryJson['amount_ot']
                        ? ($payslip['gross_salary'] - $salaryJson['amount_ot']) + ($salaryJson['amount_ot'] + ($amount - $salaryJson['amount_ot']))
                        : $payslip['gross_salary'];

                    $salaryJson['amount_ot'] = $amount;
                    $salaryJson['hrs'] = $hrs;
                }
                if ($employee->type == Employee::TYPE_EXPAT) {
                    $grossSalary = $realSalary * $rateUSDtoLAK;
                } else {
                    $grossSalary = $realSalary;
                }

                $personalIncomeTax = $this->calcPersonalIncomeTax($grossSalary); // personal income tax
                $salaryJson['personal_income_tax'] = $employee->type == Employee::TYPE_EXPAT
                    ? $personalIncomeTax * $rateLAKtoUSD
                    : $personalIncomeTax;

                $netSalary = $grossSalary - $personalIncomeTax;
                $salaryJson['salary_convert'] = $netSalary * $rateLAKtoVND;
                $salaryJson['salary_convert_usd'] = $netSalary * $rateLAKtoUSD;

                $payslip->update([
                    'salary_json' => $salaryJson,
                    'gross_salary' => $employee->type == Employee::TYPE_EXPAT ? $grossSalary * $rateLAKtoUSD : $grossSalary,
                    'net_salary' => $employee->type == Employee::TYPE_EXPAT ? $netSalary * $rateLAKtoUSD : $netSalary,
                ]);
            }

            DB::commit();

            return $payslip;
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
    }

    public function createOvertime(array $attrs)
    {
        try {
            DB::beginTransaction();

            $rates = data_get($attrs, 'rates');
            foreach (data_get($attrs, 'details') as $item) {
                if ($item['total_amount'] == 0) {
                    continue;
                }
                $data = $this->overtimeRepository->updateOrCreate([
                    'employee_id' => $item['employee_id'],
                    'overtime_date' => Carbon::make(data_get($attrs, 'overtime_date'))->format('Y-m-d'),
                ], [
                    'rates' => Arr::map($item['rates'], fn ($value) => (float) $value),
                ]);
                // TODO: calc it
                $totalAmount = $item['total_amount'];
                $totalHrs = collect($data->rates)->values()->reduce(function ($carry, $item) {
                    return $carry + $item;
                }, 0);

                $data->update([
                    'total_amount' => $totalAmount,
                    'total_hrs' => $totalHrs,
                ]);

                foreach ($rates as $rate) {
                    $data->exchangeRates()->create([
                        'from_currency_code' => $rate['from_currency_code'],
                        'to_currency_code' => $rate['to_currency_code'],
                        'rate' => $rate['rate'],
                    ]);
                }

                $this->updateOvertimePayslip($totalAmount, $totalHrs, $data, false, $rates);
            }

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
    }

    public function editOvertime($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            foreach (data_get($attrs, 'details') as $item) {
                $data = $this->overtimeRepository->updateOrCreate([
                    'id' => $id,
                    'employee_id' => $item['employee_id'],
                    'overtime_date' => Carbon::make(data_get($attrs, 'overtime_date'))->format('Y-m-d'),
                ], [
                    'rates' => Arr::map($item['rates'], fn ($value) => (float) $value),
                ]);

                // TODO: calc it
                $totalAmount = $item['total_amount'];
                $totalHrs = collect($data->rates)->values()->reduce(function ($carry, $item) {
                    return $carry + $item;
                }, 0);

                $data->update([
                    'total_amount' => $totalAmount,
                    'total_hrs' => $totalHrs,
                ]);

                $this->updateOvertimePayslip($totalAmount, $totalHrs, $data);
            }

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
    }

    public function deleteOvertime($id)
    {
        try {
            DB::beginTransaction();

            $overtime = $this->overtimeRepository->find($id);
            //update salary slip
            $this->updateOvertimePayslip($overtime->total_amount, $overtime->total_hrs, $overtime, true);

            $this->overtimeRepository->delete($id);

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
    }

    public function deleteOvertimes(array $ids)
    {
        try {
            DB::beginTransaction();

            $overtimes = $this->overtimeRepository->findWhere([
                ['id', 'IN', $ids],
            ]);

            foreach ($overtimes as $overtime) {
                //update salary slip
                $this->updateOvertimePayslip($overtime->total_amount, $overtime->total_hrs, $overtime, true);
            }

            $this->overtimeRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }
    }
}
