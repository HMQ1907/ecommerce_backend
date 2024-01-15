<?php

namespace Modules\Users\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Departments\Models\Department;
use Modules\Designations\Models\Designation;
use Modules\Employees\Models\Employee;
use Modules\Payroll\Models\EmployeeSalary;

class ContractorImport implements ToCollection, WithCalculatedFormulas, WithStartRow
{
    use Importable;

    protected $branchId;

    protected $headers;

    public function __construct($branchId, $headers)
    {
        $this->branchId = $branchId;
        $this->headers = $headers;
    }

    public function collection(Collection $rows)
    {
        $index = Employee::query()->where('branch_id', $this->branchId)->max('sort_order') + 1;
        foreach ($rows as $row) {
            $title = data_get($row, $this->headers['title']);
            if ($title) {
                $department = Department::query()->firstWhere([
                    'branch_id' => $this->branchId,
                    'name' => trim(data_get($row, $this->headers['department'])),
                ]);
                $designation = Designation::query()->firstOrCreate([
                    'name' => trim(data_get($row, $this->headers['designation'])),
                ]);

                $name = data_get($row, $this->headers['name']);
                $nameArr = explode(' ', $name);
                $lastName = $nameArr[count($nameArr) - 1];
                unset($nameArr[count($nameArr) - 1]);
                $firstName = implode(' ', $nameArr);

                $employee = Employee::create([
                    'sort_order' => $index++,
                    'branch_id' => $this->branchId,
                    'employee_code' => trim(data_get($row, $this->headers['employee_code'])),
                    'type' => 'contractor',
                    // ], [
                    'department_id' => optional($department)->id,
                    'designation_id' => $designation->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'indicator' => trim(data_get($row, $this->headers['indicator'])),
                    'is_insurance' => trim(data_get($row, $this->headers['is_insurance'])) == 'YES',
                    'date_of_birth' => $this->getTime(data_get($row, $this->headers['date_of_birth'])),
                    'date_to_company' => $this->getTime(data_get($row, $this->headers['date_to_company'])),
                    'job' => trim(data_get($row, $this->headers['job'])),
                    'gender' => trim(data_get($row, $this->headers['gender'])) == 'M' ? 'male' : 'female',
                    'education' => trim(data_get($row, $this->headers['education'])),
                    'created_by' => 1,
                ]);

                $contractFrom = data_get($row, $this->headers['contract_from']);
                $contractTo = data_get($row, $this->headers['contract_to']);
                if ($contractFrom) {
                    $employee->contracts()->create([
                        'type' => 'full_time',
                        'number' => $employee->employee_code,
                        'contract_from' => $this->getTime($contractFrom),
                        'contract_to' => $this->getTime($contractTo),
                        'created_by' => 1,
                    ]);
                }

                $BCEL = data_get($row, $this->headers['bcel_bank']);
                if ($BCEL) {
                    $employee->bankAccounts()->create([
                        'bank_name' => 'BCEL',
                        'account_number' => trim($BCEL),
                    ]);
                }

                $LDB = data_get($row, $this->headers['lbd_bank']);
                if ($LDB) {
                    $employee->bankAccounts()->create([
                        'bank_name' => 'LDB',
                        'account_number' => trim($LDB),
                    ]);
                }

                $salary = data_get($row, $this->headers['salary']);
                if ($salary) {
                    $data = EmployeeSalary::query()->firstOrCreate([
                        'employee_id' => $employee->id,
                        'type' => 'initial',
                    ], [
                        'basic_salary' => $salary,
                        'current_basic_salary' => $salary,
                        'social_security' => $employee->is_insurance ? $this->calculateSocialSecurity($salary) : 0,
                        'retirement_fund' => (int) $salary * 0.147,
                        'insurance_salary' => $this->calculateInsuranceSalary($salary),
                        'date' => now(),
                        'created_by' => 1,
                    ]);

                    $positionHousing = data_get($row, $this->headers['housing']);
                    if ($positionHousing) {
                        $data->variableSalaries()->create([
                            'variable_component_id' => 1,
                            'variable_value' => $positionHousing,
                            'current_value' => $positionHousing,
                            'adjustment_type' => 'initial',
                        ]);
                    }

                    $newPosition = data_get($row, $this->headers['position']);
                    if ($newPosition) {
                        $data->variableSalaries()->create([
                            'variable_component_id' => 2,
                            'variable_value' => $newPosition,
                            'current_value' => $newPosition,
                            'adjustment_type' => 'initial',
                        ]);
                    }
                }
            }
        }
    }

    protected function calculateSocialSecurity($basicSalary)
    {
        if ($basicSalary >= 4500000) {
            $socialSecurity = 4500000 * 0.055;
        } else {
            $socialSecurity = $basicSalary * 0.055;
        }

        return $socialSecurity;
    }

    protected function calculateInsuranceSalary($basicSalary)
    {
        if ($basicSalary > 4500000) {
            return 4500000 * 0.06;
        } else {
            return $basicSalary * 0.06;
        }
    }

    public function getTime($value)
    {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        }

        return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function startRow(): int
    {
        return 8;
    }
}
