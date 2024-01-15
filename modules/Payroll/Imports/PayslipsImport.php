<?php

namespace Modules\Payroll\Imports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Payroll\Models\SalarySlip;

class PayslipsImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    protected $rules;

    protected $month;

    const TAX_5_PERCENT = 3700000;

    const TAX_10_PERCENT = 10000000;

    const TAX_15_PERCENT = 10000000;

    public function __construct($rules, $month)
    {
        $this->rules = $rules;
        $this->month = $month;
    }

    public function collection(Collection $collection)
    {
        try {
            DB::beginTransaction();

            $start = Carbon::parse($this->month)->startOfMonth()->format('Y-m-d');
            $end = Carbon::parse($this->month)->endOfMonth()->format('Y-m-d');
            $codes = $collection->pluck('employee_code');

            $payslips = SalarySlip::query()
                ->with(['employee' => function ($query) {
                    $query->select('id', 'employee_code');
                }])
                ->whereHas('employee', function ($query) use ($codes) {
                    $query->whereIn('employee_code', $codes);
                })
                ->where('salary_from', $start)
                ->where('salary_to', $end)
                ->where('status', '!=', SalarySlip::PAID)
                ->get();

            foreach ($collection as $row) {
                $payslip = $payslips->firstWhere('employee.employee_code', $row['employee_code']);
                $salaryJson = $payslip['salary_json'];

                $salaryDay = $salaryJson['basic_salary'] / $salaryJson['expected_working_days']; // salary per day
                $salaryJson['amount_ot'] = $row['amount_ot']; // amount of OT
                $salaryJson['hrs'] = $row['hrs']; // amount of OT
                $salaryJson['advance'] = $row['advance']; // salary advance
                $salaryJson['actual_working_days'] = $row['actual_working_days']; // actual working days

                $salaryJson['real_salary'] = ($salaryDay * $salaryJson['actual_working_days']) + $salaryJson['amount_ot'] + $salaryJson['fixed_allowance'] - $salaryJson['advance'] - $salaryJson['social_security']; // real salary
                $salaryJson['personal_income_tax'] = $this->calcPersonalIncomeTax($salaryJson['real_salary']); // personal income tax
                $netSalary = $salaryJson['real_salary'] - $salaryJson['personal_income_tax']; // net salary

                // salary convert
                $salaryJson['salary_convert'] = $netSalary * $salaryJson['rate'];

                $payslip->update([
                    'salary_json' => $salaryJson,
                    'net_salary' => $netSalary,
                    'gross_salary' => $salaryJson['real_salary'],
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function calcPersonalIncomeTax($totalSalary)
    {
        if ($totalSalary < 1300000) {
            return 0;
        } elseif ($totalSalary > 1300000 && $totalSalary < 5000000) {
            $tax5Percent = $this->tax5Percent($totalSalary);

            return $tax5Percent * 0.05;
        } elseif ($totalSalary > 5000000 && $totalSalary < 15000000) {
            $tax10Percent = $this->tax10Percent($totalSalary);

            return self::TAX_5_PERCENT * 0.05 + $tax10Percent * 0.1;
        } elseif ($totalSalary > 15000000 && $totalSalary < 25000000) {
            $tax15Percent = $this->tax15Percent($totalSalary);

            return self::TAX_5_PERCENT * 0.05 + self::TAX_10_PERCENT * 0.1 + $tax15Percent * 0.15;
        } elseif ($totalSalary > 25000000 && $totalSalary < 65000000) {
            $tax20Percent = $this->tax20Percent($totalSalary);

            return self::TAX_5_PERCENT * 0.05 + self::TAX_10_PERCENT * 0.1 + self::TAX_15_PERCENT * 0.15 + $tax20Percent * 0.2;
        } else {
            return 5565000;
        }
    }

    public function tax5Percent($totalSalary)
    {
        if ($totalSalary < 1300000) {
            return 0;
        } elseif ($totalSalary <= 5000000) {
            return $totalSalary - 1300000;
        } else {
            return 5000000 - 1300000;
        }
    }

    public function tax10Percent($totalSalary)
    {
        if ($totalSalary <= 5000000) {
            return 0;
        } elseif ($totalSalary <= 15000000) {
            return $totalSalary - 5000000;
        } else {
            return 15000000 - 5000000;
        }
    }

    public function tax15Percent($totalSalary)
    {
        if ($totalSalary <= 15000000) {
            return 0;
        } elseif ($totalSalary <= 25000000) {
            return $totalSalary - 15000000;
        } else {
            return 25000000 - 15000000;
        }
    }

    public function tax20Percent($totalSalary)
    {
        if ($totalSalary <= 25000000) {
            return 0;
        } elseif ($totalSalary <= 65000000) {
            return $totalSalary - 25000000;
        } else {
            return 65000000 - 25000000;
        }
    }
}
