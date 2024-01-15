<?php

namespace Modules\Users\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Designations\Models\Designation;
use Modules\Employees\Models\Employee;
use Modules\Payroll\Models\EmployeeSalary;

class RemovalImport implements ToCollection, WithCalculatedFormulas, WithStartRow
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
        $index = Employee::query()->where('branch_id', $this->branchId)->max('sort_order');
        foreach ($rows as $row) {
            $title = data_get($row, $this->headers['title']);
            if ($title) {
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
                    'type' => 'removal',
                    // ], [
                    'designation_id' => $designation->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'indicator' => trim(data_get($row, $this->headers['indicator'])),
                    'date_of_birth' => $this->getTime(data_get($row, $this->headers['date_of_birth'])),
                    'date_to_company' => $this->getTime(data_get($row, $this->headers['date_to_company'])),
                    'job' => trim(data_get($row, $this->headers['job'])),
                    'gender' => trim(data_get($row, $this->headers['gender'])) == 'M' ? 'male' : 'female',
                    'created_by' => 1,
                ]);

                $employee->terminationAllowances()->create([
                    'subject' => trim(data_get($row, $this->headers['remarks'])),
                    'type' => trim(data_get($row, $this->headers['remarks'])),
                    'notice_date' => $this->getTime(data_get($row, $this->headers['last_day'])),
                    'termination_date' => $this->getTime(data_get($row, $this->headers['last_day'])),
                    'terminated_by' => 1,
                ]);

                $salary = data_get($row, $this->headers['salary']);
                if ($salary) {
                    $data = EmployeeSalary::query()->firstOrCreate([
                        'employee_id' => $employee->id,
                        'currency_code' => Str::of($employee->employee_code)->contains('E') ? 'USD' : 'LAK',
                        'type' => 'initial',
                    ], [
                        'basic_salary' => $salary,
                        'current_basic_salary' => $salary,
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

    public function getTime($value)
    {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        }

        return null;
    }

    public function startRow(): int
    {
        return 8;
    }
}
