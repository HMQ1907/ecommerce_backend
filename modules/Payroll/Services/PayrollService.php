<?php

namespace Modules\Payroll\Services;

use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeAward;
use Modules\Payroll\Exports\PayslipsRequestErrorExport;
use Modules\Payroll\Imports\PayslipsImport;
use Modules\Payroll\Models\EmployeeSalary;
use Modules\Payroll\Models\SalarySlip;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PayrollService extends BaseService
{
    const TAX_5_PERCENT = 3700000;

    const TAX_10_PERCENT = 10000000;

    const TAX_15_PERCENT = 10000000;

    public function generatePaySlips(array $params)
    {
        $month = Carbon::parse(data_get($params, 'month'));
        $rates = data_get($params, 'rates');
        $startMonth = $month->startOfMonth()->format('Y-m-d');
        $endMonth = $month->endOfMonth()->format('Y-m-d');

        $expectedSalaryDays = $this->getWorkingDays($startMonth, $endMonth);

        $employees = Employee::query()
            ->active()
            ->whereDate('date_to_company', '<=', $startMonth)
            ->whereHas('salaries')->get();

        $slips = [];

        foreach ($employees as $employee) {
            // $paidSlip = SalarySlip::query()
            //     ->where('employee_id', $employee->id)
            //     ->where('salary_from', $startMonth)
            //     ->where('salary_to', $endMonth)
            //     ->first();

            // if (!empty($paidSlip)) {
            //     break;
            // }

            // Convert Lao currency to USD
            $rateLAKtoVND = 0;
            $rateLAKtoUSD = 0;
            $rateUSDtoLAK = 0;

            foreach ($rates as $rate) {
                if ($rate['to_currency_code'] == 'VND') {
                    $rateLAKtoVND = $rate['rate'];
                } else {
                    $rateUSDtoLAK = $rate['rate'];
                    $rateLAKtoUSD = 1 / $rate['rate'];
                }
            }

            $employeeSalary = EmployeeSalary::query()
                ->with('variableSalaries.component')
                ->where('employee_id', $employee->id)
                ->whereDate('date', '<=', $endMonth)
                ->latest()
                ->first();

            $totalBasicSalary = $employeeSalary['current_basic_salary'];

            // $workingDays = count($attendanceDays) ;
            $workingDays = 26;

            $addDiff = 0;
            // allowances
            $mainAllowances = [];
            if (!empty($employeeSalary)) {
                foreach ($employeeSalary['variableSalaries'] as $component) {
                    if ($component['variable_component_id'] === 3) {
                        $addDiff = $component['current_value'];
                    }
                    $mainAllowances[] = [
                        'component_id' => $component['variable_component_id'],
                        'name' => $component['component']['name'],
                        'total' => $component['current_value'],
                        'convert_value' => $employee->type === Employee::TYPE_EXPAT ? number_format(($component['current_value'] * $rateUSDtoLAK) * $rateLAKtoVND, 2) : number_format($component['current_value'], 2),
                    ];
                }
            }
            $mainAllowanceTotal = collect($mainAllowances)->sum('total');

            // Social security
            if ($employee->type == Employee::TYPE_EXPAT || !$employee->is_insurance) {
                $socialSecurity = 0;
            } else {
                $socialSecurity = $this->calculateSocialSecurity($employeeSalary->current_basic_salary);
            }

            // OT, advance
            $salaryOT = 0; // OT amount
            $advance = 0;
            $retaliation = $employee->retaliations()
                ->whereDate('increment_date', '<=', $endMonth)
                ->first(); // Truy lĩnh
            $retaliationAmount = data_get($retaliation, 'original_amount_of_money', 0);
            $hrs = 0;

            // incent money
            $incentiveMoney = 0;

            // implement rate by type employee
            $salaryAfterSocialSecurity = $totalBasicSalary - $socialSecurity; // salary after social security

            // bonus
            $bonus = EmployeeAward::query()->where('employee_id', $employee->id)
                ->whereHas('award', function ($query) use ($params) {
                    $query->whereMonth('award_period', Carbon::parse(data_get($params, 'month'))->month);
                })
                ->get();

            // bonus tax
            $bonusTax = $bonus->where('is_tax', true)->sum('amount');

            //total bonus
            $totalBonus = collect($bonus)->sum('amount');

            if ($employee->type === Employee::TYPE_EXPAT) {
                // salary calculation values -> convert USD to LAK
                $realSalary = ($salaryAfterSocialSecurity + $mainAllowanceTotal + $salaryOT + $retaliationAmount + $totalBonus) * $rateUSDtoLAK; // real salary

                // personal income tax re-generate
                if ($bonusTax) {
                    $personalIncomeTax = $this->calcPersonalIncomeTax(($realSalary - ($totalBonus * $rateUSDtoLAK)) + ($bonusTax * $rateUSDtoLAK)); // personal income tax
                } else {
                    $personalIncomeTax = $this->calcPersonalIncomeTax($realSalary - ($totalBonus * $rateUSDtoLAK)); // personal income tax
                }

                $netSalary = ($realSalary + $totalBonus) - $personalIncomeTax; // net salary
            } else {
                // salary calculation values
                $realSalary = $salaryAfterSocialSecurity + $mainAllowanceTotal + $salaryOT + $retaliationAmount + $totalBonus; // real salary

                // personal income tax re-generate
                if ($bonusTax) {
                    $personalIncomeTax = $this->calcPersonalIncomeTax(($realSalary - $totalBonus) + $bonusTax); // personal income tax
                } else {
                    $personalIncomeTax = $this->calcPersonalIncomeTax($realSalary - $totalBonus); // personal income tax
                }

                $netSalary = $realSalary - $personalIncomeTax; // net salary
            }

            $slip = SalarySlip::query()->updateOrCreate([
                'employee_id' => $employee->id,
                'salary_from' => $startMonth,
                'salary_to' => $endMonth,
            ], [
                'salary_group_id' => null,
                'salary_json' => [
                    'basic_salary' => $totalBasicSalary, // basic salary
                    'main_allowances' => $mainAllowances, // main allowances
                    'real_salary' => $realSalary, // real salary
                    'rate_lak_to_vnd' => $rateLAKtoVND, // rate LAK to VND
                    'rate_lak_to_usd' => $rateLAKtoUSD, // rate LAK to USD
                    'rate_usd_to_lak' => $rateUSDtoLAK, // rate USD to LAK
                    'advance' => $advance, // advance
                    'social_security' => $socialSecurity, // social security
                    'expected_working_days' => $expectedSalaryDays, // expected working days
                    'actual_working_days' => $workingDays, // actual working days
                    'amount_ot' => $salaryOT, // OT amount
                    'retaliation' => $retaliationAmount, // Truy lĩnh
                    'hrs' => $hrs, // OT hours
                    'incent_money' => $incentiveMoney, // Incentive money
                    'add_diff' => $addDiff, // Add diff
                    'personal_income_tax' => $personalIncomeTax, // Personal income tax
                    'salary_after_social_security' => $salaryAfterSocialSecurity, // Salary after social security
                    'retirement_fund' => $employeeSalary->retirement_fund, // Retirement fund
                    'insurance_salary' => $employeeSalary->insurance_salary, // Insurance salary
                    'fixed_allowance' => $mainAllowanceTotal, // Fixed allowance
                    'salary_convert' => $netSalary * $rateLAKtoVND, // Salary convert VND
                    'salary_convert_usd' => $netSalary * $rateLAKtoUSD, // Salary convert USD
                ],
                'extra_json' => [
                    'earnings' => [],
                    'deductions' => [],
                    'total_earning' => 0,
                    'total_deduction' => 0,
                ],
                'gross_salary' => $realSalary, // Gross salary
                'net_salary' => $netSalary, // Net salary
            ]);

            if ($employee->type === Employee::TYPE_EXPAT) {
                $salaryJson = $slip['salary_json'];
                $salaryJson['personal_income_tax'] = $salaryJson['personal_income_tax'] * $rateLAKtoUSD; // personal income tax
                // $salaryJson['salary_convert'] = $netSalary * $rateLAKtoVND;
                // $salaryJson['salary_convert_usd'] = $netSalary * $rateLAKtoUSD;
                $slip->update([
                    'salary_json' => $salaryJson,
                    'gross_salary' => $realSalary * $rateLAKtoUSD,
                    'net_salary' => $netSalary * $rateLAKtoUSD,
                ]);
            }

            foreach ($rates as $rate) {
                $slip->exchangeRates()->create([
                    'from_currency_code' => $rate['from_currency_code'],
                    'to_currency_code' => $rate['to_currency_code'],
                    'rate' => $rate['rate'],
                ]);
            }
            $slip->save();

            $slips[] = $slip;
        }

        return $slips;
    }

    public function generateComponents($components, $amount = 0, $isBusinessFee = false)
    {
        $newComponents = [];

        foreach ($components as $component) {
            $newComponents[] = [
                'component_id' => $component['id'],
                'name' => $component['name'],
                'value' => $component['value'],
                'count' => $isBusinessFee ? 0 : null,
                'total' => $isBusinessFee ? 0 : $component['value'] * $amount,
            ];
        }

        return $newComponents;
    }

    public function getPayslips(array $params)
    {
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
            ->join('employees', 'employees.id', '=', 'salary_slips.employee_id')
            ->orderBy('employees.sort_order')
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->whereHas('employee', function ($query) use ($q) {
                        $query->searchName($q);
                    });
                }, null, ''),
                AllowedFilter::callback('month', function (Builder $query, $month) {
                    $query->where(function ($query) use ($month) {
                        $query->whereMonth('salary_from', Carbon::parse($month)->month)
                            ->whereYear('salary_from', Carbon::parse($month)->year);
                    });
                }),
                AllowedFilter::callback('employee_type', function (Builder $query, $employeeType) {
                    $query->whereHas('employee', function ($query) use ($employeeType) {
                        $type = is_array($employeeType) ? $employeeType : [$employeeType];
                        if (in_array(Employee::TYPE_REMOVAL, $type)) {
                            $query->withoutGlobalScope('active');
                        }
                        $query->whereIn('type', $type);
                    });
                }),
            ])
            // ->allowedSorts(['created_at'])
            // ->defaultSorts(AllowedSort::callback('sort_order', function (Builder $query, bool $descending) {
            //     $direction = $descending ? 'DESC' : 'ASC';
            //     $query
            // }))
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function payPaySlips($attr)
    {
        $ids = data_get($attr, 'ids');
        $payslips = SalarySlip::query()->whereIn('id', $ids)->get();

        foreach ($payslips as $payslip) {
            if ($payslip->status === SalarySlip::PAID) {
                continue;
            }
            $payslip->update([
                'paid_on' => now(),
                'status' => SalarySlip::PAID,
            ]);
        }
    }

    public function updatePayslip(array $attr, $id)
    {
        $salarySlip = SalarySlip::findOrFail($id);

        $salaryJson = $salarySlip['salary_json'];
        // $salaryDay = $salaryJson['basic_salary'] / $salaryJson['expected_working_days']; // salary per day
        // $salaryJson['real_salary'] = ($salaryDay * $salaryJson['actual_working_days']) + data_get($attr, 'salary_json.amount_ot') + $salaryJson['fixed_allowance'] - $salaryJson['advance'] - $salaryJson['social_security']; // real salary
        // $salaryJson['amount_ot'] = data_get($attr, 'salary_json.amount_ot');

        $rateLAKtoVND = $salaryJson['rate_lak_to_vnd'];
        $rateUSDtoLAK = $salaryJson['rate_usd_to_lak'];

        $earningsExtraTotal = collect($attr['extra_json']['earnings'])->sum('value');
        $deductionsExtraTotal = collect($attr['extra_json']['deductions'])->sum('value');

        // $grossSalary = $salaryJson['real_salary'] + $earningsExtraTotal;
        $netSalary = max($salarySlip['gross_salary'] - $salaryJson['personal_income_tax'] - $deductionsExtraTotal, 0);

        $attr['extra_json']['total_earning'] = $earningsExtraTotal;
        $attr['extra_json']['total_deduction'] = $deductionsExtraTotal;
        $salaryJson['salary_convert'] = ($netSalary * $rateUSDtoLAK) * $rateLAKtoVND;

        $salarySlip->update([
            'paid_on' => data_get($attr, 'paid_on'),
            'status' => data_get($attr, 'status'),
            'salary_json' => $salaryJson,
            'extra_json' => data_get($attr, 'extra_json'),
            'net_salary' => $netSalary,
            'total_deductions' => $deductionsExtraTotal,
        ]);
    }

    public function exportPayslip($id)
    {
        return SalarySlip::with('employee')->find($id);
    }

    public function importPayslips($request)
    {
        try {
            $file = $request->file('file');
            $month = $request->month;

            $rules = [
                '*.employee_code' => ['required'],
                '*.amount_ot' => ['required', 'min:0', 'numeric'],
                '*.advance' => ['required', 'min:0', 'numeric'],
                '*.actual_working_days' => ['required', 'min:0', 'numeric'],
                '*.hrs' => ['required', 'min:0', 'numeric'],
            ];

            $import = new PayslipsImport($rules, $month);

            $data = Excel::toArray($import, $file);

            $validator = Validator::make($data[0], $rules);

            if ($validator->fails()) {
                $export = new PayslipsRequestErrorExport($data[0], $validator->invalid());

                return Excel::download($export, now()->format('H:i:s').'-error_'.$file->getClientOriginalName());
            }

            Excel::import($import, $file);

            return responder()->success()->respond();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
