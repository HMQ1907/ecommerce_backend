<?php

namespace Modules\Employees\Services;

use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Models\Award;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeAward;
use Modules\Employees\Repositories\AwardRepository;
use Modules\Employees\Repositories\EmployeeAwardRepository;
use Modules\Employees\Repositories\EmployeeRepository;
use Modules\Payroll\Models\SalarySlip;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeAwardService extends BaseService
{
    protected $awardRepository;

    protected $employeeAwardRepository;

    protected $employeeRepository;

    public function __construct(AwardRepository $awardRepository, EmployeeAwardRepository $employeeAwardRepository, EmployeeRepository $employeeRepository)
    {
        $this->awardRepository = $awardRepository;
        $this->employeeAwardRepository = $employeeAwardRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function getEmployeeAwards(array $params)
    {
        return QueryBuilder::for(Award::allData())
            ->with([
                'employeeAwards',
                'employeeAwards.employee',
            ])
            ->allowedFilters([
                AllowedFilter::callback('month', function (Builder $query, $month) {
                    $query->where(function ($query) use ($month) {
                        $query->whereMonth('award_period', Carbon::parse($month)->month)
                            ->whereYear('award_period', Carbon::parse($month)->year);
                    });
                }),
                AllowedFilter::callback('employee_id', function (Builder $query, $employeeId) {
                    $query->whereHas('employeeAwards', function ($query) use ($employeeId) {
                        $query->where('employee_id', $employeeId);
                    });
                }),
            ])
            ->defaultSort('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getEmployeeAward($id)
    {
        return $this->awardRepository->find($id);
    }

    public function getEmployeeOfAwards($id)
    {
        $award = $this->awardRepository->find($id);

        return $award->employeeAwards;
    }

    protected function updatePaySlip($employee, $delete, $amount, $rates = [], $isTaxChange = false, $amountBefore = 0)
    {
        $startMonth = now()->startOfMonth()->format('Y-m-d');
        $endMonth = now()->endOfMonth()->format('Y-m-d');
        $employeeSlip = $this->employeeRepository->find($employee['employee_id']);

        // check $employeeSlip type employee
        $isExpat = $employeeSlip->type == Employee::TYPE_EXPAT;
        $payslip = SalarySlip::query()
            ->where('employee_id', $employee['employee_id'])
            ->where('salary_from', $startMonth)
            ->where('salary_to', $endMonth)
            ->first();

        if (!empty($payslip)) {
            // rate
            $salaryJson = $payslip['salary_json'];
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

            // check $personalIncomeTax type employee
            $personalIncomeTax = $isExpat ? $salaryJson['personal_income_tax'] * $rateUSDtoLAK : $salaryJson['personal_income_tax']; // personal income tax

            if ($delete) {
                $realSalary = $isExpat ? ($payslip['gross_salary'] - $amount) * $rateUSDtoLAK : $payslip['gross_salary'] - $amount; // real salary

                //update $personalIncomeTax delete award
                $amountDelete = $isExpat ? $amount * $rateUSDtoLAK : $amount;
                $taxAmount = $this->calcPersonalIncomeTax($amountDelete);
                $personalIncomeTax = $personalIncomeTax - $taxAmount;
            } else {
                $realSalary = $isExpat ? ($payslip['gross_salary'] + $amount) * $rateUSDtoLAK : $payslip['gross_salary'] + $amount; // real salary
            }

            // personal income tax when change is_tax
            if ($employee['is_tax'] == 1 && $isTaxChange == false) {
                $personalIncomeTax = $this->calcPersonalIncomeTax($realSalary);
            } elseif ($isTaxChange) {
                $personalIncomeTax = $isExpat
                    ? $this->calcPersonalIncomeTax($realSalary - ($amountBefore * $rateUSDtoLAK))
                    : $this->calcPersonalIncomeTax($realSalary - $amountBefore);
            }

            // update payslip
            $netSalary = $realSalary - $personalIncomeTax; // net salary
            $salaryJson['personal_income_tax'] = $isExpat
                ? $personalIncomeTax * $rateLAKtoUSD
                : $personalIncomeTax; // personal income tax

            $salaryJson['salary_convert'] = $netSalary * $rateLAKtoVND; // salary convert vnd
            $salaryJson['salary_convert_usd'] = $netSalary * $rateLAKtoUSD; // salary convert vnd
            $payslip->update([
                'salary_json' => $salaryJson,
                'gross_salary' => $isExpat ? $realSalary * $rateLAKtoUSD : $realSalary,
                'net_salary' => $isExpat ? $netSalary * $rateLAKtoUSD : $netSalary,
            ]);

            $payslip->save();
        }
    }

    public function createEmployeeAward(array $attrs)
    {
        try {
            DB::beginTransaction();

            $award = $this->awardRepository->create([
                'title' => data_get($attrs, 'title'),
                'award_type' => data_get($attrs, 'award_type'),
                'type' => data_get($attrs, 'type'),
                'award_period' => now(),
            ]);

            $totalAmount = 0;
            $rates = data_get($attrs, 'rates');
            $rateLAKtoUSD = 0;
            foreach ($rates as $rate) {
                if ($rate['to_currency_code'] !== 'VND') {
                    $rateLAKtoUSD = 1 / $rate['rate'];
                }
            }

            foreach (data_get($attrs, 'employees') as $employee) {
                if (data_get($attrs, 'type') == 'birthday' && $employee['type'] == 'expat') {
                    $amount = $employee['amount'] * $rateLAKtoUSD;
                } else {
                    $amount = $employee['amount'];
                }

                $totalAmount += $amount;
                $employeeAward = $this->employeeAwardRepository->create([
                    'employee_id' => $employee['employee_id'],
                    'award_id' => $award->id,
                    'amount' => $amount,
                    'amount_tax' => $employee['is_tax'] ? $this->calcPersonalIncomeTax($amount) : 0,
                    'is_tax' => $employee['is_tax'] ?? false,
                ]);

                foreach ($rates as $rate) {
                    $employeeAward->exchangeRates()->create([
                        'from_currency_code' => $rate['from_currency_code'],
                        'to_currency_code' => $rate['to_currency_code'],
                        'rate' => $rate['rate'],
                    ]);
                }

                //update salary slip
                $this->updatePaySlip($employee, false, $amount, $rates);
            }

            $award->update([
                'total_amount' => $totalAmount,
            ]);

            DB::commit();

            return $award;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editEmployeeAward(array $attrs, $awardId)
    {
        try {
            DB::beginTransaction();

            $values = [];
            $totalAmount = 0;

            if (isset($attrs['title'])) {
                $values['title'] = $attrs['title'];
            }
            if (isset($attrs['award_type'])) {
                $values['award_type'] = $attrs['award_type'];
            }
            if (isset($attrs['award_period'])) {
                $values['award_period'] = now();
            }
            if (isset($attrs['type'])) {
                $values['type'] = $attrs['type'];
            }

            $award = $this->awardRepository->update($values, $awardId);
            foreach (data_get($attrs, 'employees') as $employee) {
                $bonusBefore = $this->employeeAwardRepository->findWhere([
                    ['award_id', '=', $award->id],
                    ['employee_id', '=', $employee['employee_id']],
                ]);

                $totalAmount += $employee['amount'];
                $award->update(['total_amount' => $totalAmount]);
                $this->employeeAwardRepository->updateOrCreate(
                    [
                        'award_id' => $award->id,
                        'employee_id' => $employee['employee_id'],
                    ],
                    [
                        'amount' => $employee['amount'],
                        'amount_tax' => $employee['is_tax'] ? $this->calcPersonalIncomeTax($employee['amount']) : 0,
                        'is_tax' => $employee['is_tax'] ?? false,
                    ]);

                // check update is_tax
                $isTaxChange = false;
                if ($bonusBefore->isNotEmpty()) {
                    $amount = $employee['amount'] - $bonusBefore[0]['amount'];
                    if ($employee['is_tax'] != $bonusBefore[0]['is_tax'] && $bonusBefore[0]['is_tax'] == true) {
                        $isTaxChange = true;
                    }
                } else {
                    $amount = $employee['amount'];
                }

                //update salary slip
                if ($bonusBefore->isNotEmpty()) {
                    $this->updatePaySlip($employee, false, $amount, [], $isTaxChange, $bonusBefore[0]['amount']);
                } else {
                    $this->updatePaySlip($employee, false, $amount, [], $isTaxChange);
                }
            }

            DB::commit();

            return $award;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteAward($id)
    {
        try {
            DB::beginTransaction();

            $award = $this->awardRepository->find($id);
            foreach ($award->employeeAwards as $employeeAward) {
                $this->updatePaySlip($employeeAward, true, $employeeAward['amount']);
            }

            $this->awardRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteAwards(array $ids)
    {
        try {
            DB::beginTransaction();

            $awards = $this->awardRepository->findWhere([
                ['id', 'IN', $ids],
            ]);
            foreach ($awards as $award) {
                foreach ($award->employeeAwards as $employeeAward) {
                    $this->updatePaySlip($employeeAward, true, $employeeAward['amount']);
                }
            }

            $this->awardRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteEmployeeAward($id)
    {
        try {
            DB::beginTransaction();

            $employeeAward = $this->employeeAwardRepository->find($id);
            $award = $this->awardRepository->find($employeeAward->award_id);

            $this->updatePaySlip($employeeAward, true, $employeeAward['amount']);
            $award->update(['total_amount' => $award->total_amount - $employeeAward->amount]);

            $this->employeeAwardRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteEmployeeAwards(array $ids)
    {
        try {
            DB::beginTransaction();

            $employeeAwards = $this->employeeAwardRepository->findWhere([
                ['id', 'IN', $ids],
            ]);

            foreach ($employeeAwards as $employeeAward) {
                $award = $this->awardRepository->find($employeeAward->award_id);
                $award->update(['total_amount' => $award->total_amount - $employeeAward->amount]);
            }

            $this->employeeAwardRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function exportEmployeeAward(array $params)
    {
        return EmployeeAward::query()->whereMonth('created_at', Carbon::parse(data_get($params, 'filter.month'))->month)
            ->select('employee_id', DB::raw('SUM(amount_tax) as total_amount_tax'), DB::raw('SUM(amount) as total_amount'))
            ->whereYear('created_at', Carbon::parse(data_get($params, 'filter.month'))->year)
            ->whereHas('employee', function (Builder $query) {
                $query->where('branch_id', auth()->user()->branch_id);
            })
            ->with([
                'employee',
                'employee.bankAccounts',
                'employee.designation',
                'employee.department',
            ])
            ->groupBy('employee_id')
            ->get();
    }
}
