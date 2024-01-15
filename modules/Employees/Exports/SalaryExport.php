<?php

namespace Modules\Employees\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Departments\Models\Department;
use Modules\Employees\Models\Employee;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $date;

    protected $branchName;

    public function __construct($data, $date, $branchName)
    {
        $this->data = $data->filter(function ($item) {
            return $item->employee->type != Employee::TYPE_EXPAT;
        });
        $this->date = $date;
        $this->branchName = $branchName;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 40,
            'C' => 20,
            'D' => 18,
            'E' => 18,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 18,
            'J' => 18,
            'K' => 18,
            'L' => 15,
            'M' => 18,
            'N' => 18,
            'O' => 18,
            'P' => 18,
            'Q' => 18,
            'R' => 20,
            'S' => 30,
            'T' => 30,
        ];
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getCell('B1')->setValue($this->branchName);
        $sheet->getStyle('B1')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('B1')->getFont()->setSize(11);
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getCell('B2')->setValue('PERIOD:SALARY-'.$this->date);
        //Header sheet
        $sheet->getCell('A4')->setValue('No');
        $sheet->getCell('B4')->setValue('NAME');
        $sheet->getCell('C4')->setValue('BANK ACCOUNT');
        $sheet->getCell('D4')->setValue('Cost Center');
        $sheet->getCell('E4')->setValue('SALARY');
        $sheet->getCell('F4')->setValue('ADD DIFF');
        $sheet->getCell('G4')->setValue('BIRTHDAY');
        $sheet->getCell('H4')->setValue('Social Security');
        $sheet->getCell('I4')->setValue('Salary after (Security)');
        $sheet->getCell('J4')->setValue('Incent. Money');
        $sheet->getCell('K4')->setValue('Deduct Loan');
        $sheet->getCell('L4')->setValue('Position & Housing Allow.');
        $sheet->getCell('M4')->setValue('Amount O/T ');
        $sheet->getCell('N4')->setValue('OT');
        $sheet->getCell('O4')->setValue('Total Salary + O/T+ Allow.');
        $sheet->getCell('P4')->setValue('Retained Tax');
        $sheet->getCell('Q4')->setValue('Total');
        $sheet->getCell('R4')->setValue('NET PAY');
        $sheet->getCell('S4')->setValue('Social Security Fund by Comp. 6.00%');
        $sheet->getStyle('A4:T4')->getFont()->setBold(true);
        $sheet->getStyle('A4:T4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:T4')->getAlignment()->setVertical('center');
        $sheet->getStyle('A4:T4')->getAlignment()->setWrapText(true);
        $sheet->freezePane('A5');
        //Employee count sheet
        $count = count($this->data);
        $sheet->getStyle('A4:S'.($count + 4))->getBorders()->getAllBorders()->setBorderStyle('thin');
        //Binding data
        $key = 0;
        foreach ($this->data as $item) {
            //No
            $sheet->getCell('A'.($key + 5))->setValue($key + 1);
            //Name
            $sheet->getCell('B'.($key + 5))->setValue($item->employee->name);
            //Bank Account
            $sheet->getCell('C'.($key + 5))->setValue($item->employee->bankAccounts->count() > 0 ? $item->employee->bankAccounts[count($item->employee->bankAccounts) - 1]->account_number : 'CASH');
            $isCash = $sheet->getCell('C'.($key + 5))->getValue();
            if ($isCash == 'CASH') {
                $sheet->getStyle('C'.($key + 5))->getFont()->getColor()->setARGB('FF0000');
            }
            //Cost Center
            $sheet->getCell('D'.($key + 5))->setValue($item->employee->department->code ?? '');
            //Salary
            $sheet->getCell('E'.($key + 5))->setValue($item->salary_json['basic_salary']);
            $sheet->getCell('E'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //ADD DIFF
            $sheet->getCell('F'.($key + 5))->setValue($item->salary_json['retaliation'] ?? 0);
            $sheet->getCell('F'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Birthday
            $sheet->getCell('G'.($key + 5))->setValue($item->employee->bonus_dob);
            $sheet->getCell('G'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Social Security
            $sheet->getCell('H'.($key + 5))->setValue($item->salary_json['social_security']);
            $sheet->getCell('H'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Salary after (Security)
            $sheet->getCell('I'.($key + 5))->setValue($item->salary_json['salary_after_social_security']);
            $sheet->getCell('I'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Incent. Money
            $sheet->getCell('J'.($key + 5))->setValue(!empty($item->salary_json['incentive_money']) ? $item->salary_json['incentive_money'] : '');
            $sheet->getCell('J'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Deduct Loan
            $sheet->getCell('K'.($key + 5))->setValue($item->extra_json['total_deduction']);
            $sheet->getCell('K'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Position & Housing Allow.
            $sheet->getCell('L'.($key + 5))->setValue($item->salary_json['fixed_allowance']);
            $sheet->getCell('L'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Amount O/T
            $sheet->getCell('M'.($key + 5))->setValue($item->salary_json['amount_ot']);
            $sheet->getCell('M'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //OT
            $sheet->getCell('N'.($key + 5))->setValue($item->salary_json['ot'] ?? 0);
            //Total Salary + O/T+ Allow.
            $sheet->getCell('O'.($key + 5))->setValue($item->salary_json['real_salary']);
            $sheet->getCell('O'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Retained Tax
            $sheet->getCell('P'.($key + 5))->setValue($item->salary_json['personal_income_tax']);
            $sheet->getCell('P'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Total
            $sheet->getCell('Q'.($key + 5))->setValue($item->net_salary);
            $sheet->getCell('Q'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Net Pay
            $sheet->getCell('R'.($key + 5))->setValue($item->net_salary);
            $sheet->getCell('R'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Social Security Fund by Comp. 6.00%
            $sheet->getCell('S'.($key + 5))->setValue($item->salary_json['insurance_salary']);
            $sheet->getCell('S'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $key++;
        }
        //Total sheet
        $sheet->getCell('B'.($count + 5))->setValue('TOTAL');
        $sheet->getCell('E'.($count + 5))->setValue('=SUM(E5:E'.($count + 4).')');
        $sheet->getCell('E'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('H'.($count + 5))->setValue('=SUM(H5:H'.($count + 4).')');
        $sheet->getCell('H'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('I'.($count + 5))->setValue('=SUM(I5:I'.($count + 4).')');
        $sheet->getCell('I'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('J'.($count + 5))->setValue('=SUM(J5:J'.($count + 4).')');
        $sheet->getCell('J'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('L'.($count + 5))->setValue('=SUM(L5:L'.($count + 4).')');
        $sheet->getCell('L'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('M'.($count + 5))->setValue('=SUM(M5:M'.($count + 4).')');
        $sheet->getCell('M'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('N'.($count + 5))->setValue('=SUM(N5:N'.($count + 4).')');
        $sheet->getCell('N'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('O'.($count + 5))->setValue('=SUM(O5:O'.($count + 4).')');
        $sheet->getCell('O'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('P'.($count + 5))->setValue('=SUM(P5:P'.($count + 4).')');
        $sheet->getCell('P'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('Q'.($count + 5))->setValue('=SUM(Q5:Q'.($count + 4).')');
        $sheet->getCell('Q'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('R'.($count + 5))->setValue('=SUM(R5:R'.($count + 4).')');
        $sheet->getCell('R'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('S'.($count + 5))->setValue('=SUM(S5:S'.($count + 4).')');
        $sheet->getCell('S'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getStyle('B'.($count + 5).':S'.($count + 5))->getFont()->setBold(true);
        $sheet->getStyle('B'.($count + 5).':S'.($count + 5))->getBorders()->getAllBorders()->setBorderStyle('thin');

        //Footer sheet
        $departments = Department::withDepth()->having('depth', '=', 3)->whereHas('branch', function ($query) {
            $query->where('name', $this->branchName);

        })->get();
        $groupDepartment = [];
        foreach ($departments as $department) {
            $result = [];
            $departmentIds = $department->descendants->pluck('id')->toArray();
            $employees = $this->data->whereIn('employee.department_id', $departmentIds)->all();
            $result['department'] = $department->name;
            $result['salary'] = 0;
            $result['social_security'] = 0;
            $result['salary_after_social_security'] = 0;
            $result['incentive_money'] = 0;
            $result['fixed_allowance'] = 0;
            $result['amount_ot'] = 0;
            $result['ot'] = 0;
            $result['real_salary'] = 0;
            $result['personal_income_tax'] = 0;
            $result['total'] = 0;
            $result['net_salary'] = 0;
            $result['insurance_salary'] = 0;
            foreach ($employees as $employee) {
                $result['salary'] += $employee->salary_json['basic_salary'];
                $result['social_security'] += $employee->salary_json['social_security'];
                $result['salary_after_social_security'] += $employee->salary_json['salary_after_social_security'];
                $result['incentive_money'] += $employee->salary_json['incentive_money'] ?? 0;
                $result['fixed_allowance'] += $employee->salary_json['fixed_allowance'];
                $result['amount_ot'] += $employee->salary_json['amount_ot'];
                $result['ot'] += $employee->salary_json['ot'] ?? 0;
                $result['real_salary'] += $employee->salary_json['real_salary'];
                $result['personal_income_tax'] += $employee->salary_json['personal_income_tax'];
                $result['total'] += $employee->net_salary;
                $result['net_salary'] += $employee->net_salary;
                $result['insurance_salary'] += $employee->salary_json['insurance_salary'];
            }
            $groupDepartment[$department->name] = $result;
        }
        // PERPARED BY:……………………
        $sheet->getCell('B'.($count + 10))->setValue('PERPARED BY:……………………');
        //APPROVED BY:………………………
        $sheet->getCell('D'.($count + 10))->setValue('APPROVED BY:……………………');
        //PETROVIETNAM OIL LAO PETROLEUM DOMESTIC TRADING SOLE CO.,LTD. (PV OIL LAO TRADING)
        $sheet->mergeCells('A'.($count + 11).':U'.($count + 11));
        $sheet->getStyle('A'.($count + 11).':U'.($count + 11))->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A'.($count + 11).':U'.($count + 11))->getAlignment()->setVertical('center');
        $sheet->getStyle('A'.($count + 11).':U'.($count + 11))->getAlignment()->setWrapText(true);
        $sheet->getCell('A'.($count + 11))->setValue('=B1');
        //Retirement Fund Provision 14.70%-JULY-23
        $sheet->getCell('B'.($count + 12))->setValue('Retirement Fund Provision 14.70%-'.$this->date);
        $sheet->getStyle('B'.($count + 12))->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B'.($count + 12))->getAlignment()->setVertical('center');
        $sheet->getStyle('B'.($count + 12))->getAlignment()->setWrapText(true);
        $sheet->getStyle('B'.($count + 12))->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('B'.($count + 12))
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('EEECE1');
        //TOTAL
        $sheet->getCell('A'.($count + 15))->setValue('No.');
        $sheet->mergeCells('B'.($count + 15).':C'.($count + 15));
        $sheet->getCell('B'.($count + 15))->setValue('NAME');
        $sheet->getCell('D'.($count + 15))->setValue('DEPT');
        $sheet->getCell('E'.($count + 15))->setValue('SALARY');
        $sheet->getCell('F'.($count + 15))->setValue('=F4');
        $sheet->getCell('G'.($count + 15))->setValue('=G4');
        $sheet->getCell('H'.($count + 15))->setValue('Social Security');
        $sheet->getCell('I'.($count + 15))->setValue('Salary after (Security)');
        $sheet->getCell('J'.($count + 15))->setValue('Incent. Money');
        $sheet->getCell('K'.($count + 15))->setValue('Deduct Loan');
        $sheet->getCell('L'.($count + 15))->setValue('Position & Housing Allow.');
        $sheet->getCell('M'.($count + 15))->setValue('Amount O/T ');
        $sheet->getCell('N'.($count + 15))->setValue('OT Hours');
        $sheet->getCell('O'.($count + 15))->setValue('Total Salary + O/T+ Allow.');
        $sheet->getCell('P'.($count + 15))->setValue('Retained Tax');
        $sheet->getCell('Q'.($count + 15))->setValue('Total');
        $sheet->getCell('R'.($count + 15))->setValue('NET PAY');
        $sheet->getCell('S'.($count + 15))->setValue('Social Security Fund by Comp. 6.00%');
        $sheet->getCell('T'.($count + 15))->setValue('Retirement Fund Provision 15.70%');
        $sheet->getStyle('A'.($count + 15).':T'.($count + 15))->getFont()->setBold(true);
        $sheet->getStyle('A'.($count + 15).':T'.($count + 15))->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A'.($count + 15).':T'.($count + 15))->getAlignment()->setVertical('center');
        $sheet->getStyle('A'.($count + 15).':T'.($count + 15))->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.($count + 15).':T'.($count + 15 + count($groupDepartment)))->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('T'.($count + 15))
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('EEECE1');
        //Total in department
        $key = 0;
        foreach ($groupDepartment as $department) {
            $sheet->getCell('A'.($count + 16 + $key))->setValue($key + 1);
            $sheet->mergeCells('B'.($count + 16 + $key).':C'.($count + 16 + $key));
            $sheet->getCell('B'.($count + 16 + $key))->setValue($department['department']);
            $sheet->getCell('E'.($count + 16 + $key))->setValue($department['salary']);
            $sheet->getCell('E'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('H'.($count + 16 + $key))->setValue($department['social_security']);
            $sheet->getCell('H'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('I'.($count + 16 + $key))->setValue($department['salary_after_social_security']);
            $sheet->getCell('I'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('J'.($count + 16 + $key))->setValue($department['incentive_money']);
            $sheet->getCell('J'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('L'.($count + 16 + $key))->setValue($department['fixed_allowance']);
            $sheet->getCell('J'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('M'.($count + 16 + $key))->setValue($department['amount_ot']);
            $sheet->getCell('M'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('N'.($count + 16 + $key))->setValue($department['ot']);

            $sheet->getCell('O'.($count + 16 + $key))->setValue($department['real_salary']);
            $sheet->getCell('O'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('P'.($count + 16 + $key))->setValue($department['personal_income_tax']);
            $sheet->getCell('P'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('Q'.($count + 16 + $key))->setValue($department['total']);
            $sheet->getCell('Q'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('R'.($count + 16 + $key))->setValue($department['net_salary']);
            $sheet->getCell('R'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('S'.($count + 16 + $key))->setValue($department['insurance_salary']);
            $sheet->getCell('S'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('T'.($count + 16 + $key))->setValue('=E'.($count + 16 + $key).'*14.7%');
            $sheet->getCell('T'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $key++;
        }

        // PERPARED BY:……………………
        $sheet->getCell('B'.($count + 18 + count($groupDepartment)))->setValue('PERPARED BY:……………………');
        //APPROVED BY:………………………
        $sheet->getCell('D'.($count + 18 + count($groupDepartment)))->setValue('APPROVED BY:……………………');

        $sheet->getStyle('A1:T1000')->getFont()->setName('Times New Roman');
        $sheet->setShowGridlines(false);
    }

    public function registerEvents(): array
    {
        $count = count($this->data);

        return [
            AfterSheet::class => function (AfterSheet $event) use ($count) {
                $event->sheet->getDelegate()->getRowDimension('4')->setRowHeight(72.75);
                $event->sheet->getDelegate()->getRowDimension($count + 11)->setRowHeight(40);
                $event->sheet->getDelegate()->getRowDimension($count + 12)->setRowHeight(40);
            },
        ];
    }

    public function title(): string
    {
        return 'SALARY';
    }
}
