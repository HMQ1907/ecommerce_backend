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
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeAwardsExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data->filter(function ($item) {
            return $item->employee != null;
        });
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
        $sheet->getCell('B1')->setValue(auth()->user()->branch->name);
        $sheet->getStyle('B1')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('B1')->getFont()->setSize(11);
        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getCell('B2')->setValue('PROPOSAL FOR ECONOMIC CRISIS IN LAO'.now()->format('Y'));
        //Header sheet
        $sheet->getCell('A4')->setValue('No');
        $sheet->getCell('B4')->setValue('NAME');
        $sheet->getCell('C4')->setValue('BANK ACCOUNT');
        $sheet->getCell('D4')->setValue('Cost Center');
        $sheet->getCell('E4')->setValue('SALARY');
        $sheet->getCell('F4')->setValue('Bonus');
        $sheet->getCell('G4')->setValue('Retained Tax');
        $sheet->getCell('H4')->setValue('Net Pay');
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:H4')->getAlignment()->setVertical('center');
        $sheet->getStyle('A4:H4')->getAlignment()->setWrapText(true);
        $sheet->freezePane('A5');
        //Employee count sheet
        $count = count($this->data);
        $sheet->getStyle('A4:H'.($count + 4))->getBorders()->getAllBorders()->setBorderStyle('thin');
        //Binding data
        foreach ($this->data as $key => $item) {
            //No
            $sheet->getCell('A'.($key + 5))->setValue($key + 1);
            //Name
            $sheet->getCell('B'.($key + 5))->setValue($item->employee->name);
            //Bank Account
            $sheet->getCell('C'.($key + 5))->setValue($item->employee->bankAccounts->count() > 0 ? $item->employee->bankAccounts->last()->account_number : '');
            //Cost Center
            $sheet->getCell('D'.($key + 5))->setValue($item->employee->designation->code ?? '');
            //Salary
            $sheet->getCell('E'.($key + 5))->setValue(optional($item->employee->currentSalary())->current_basic_salary);
            $sheet->getCell('E'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Bonus
            $sheet->getCell('F'.($key + 5))->setValue($item->total_amount);
            $sheet->getCell('F'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Retained Tax
            $sheet->getCell('G'.($key + 5))->setValue($item->total_amount_tax);
            $sheet->getCell('G'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            //Net Pay
            $sheet->getCell('H'.($key + 5))->setValue($item->total_amount);
            $sheet->getCell('H'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        }
        //Total sheet
        $sheet->getCell('B'.($count + 5))->setValue('TOTAL');
        $sheet->getCell('E'.($count + 5))->setValue('=SUM(E5:E'.($count + 4).')');
        $sheet->getCell('E'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('F'.($count + 5))->setValue('=SUM(F5:F'.($count + 4).')');
        $sheet->getCell('F'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('G'.($count + 5))->setValue('=SUM(G5:G'.($count + 4).')');
        $sheet->getCell('G'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('H'.($count + 5))->setValue('=SUM(H5:H'.($count + 4).')');
        $sheet->getCell('H'.($count + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

        $sheet->getStyle('B'.($count + 5).':H'.($count + 5))->getFont()->setBold(true);
        $sheet->getStyle('B'.($count + 5).':H'.($count + 5))->getBorders()->getAllBorders()->setBorderStyle('thin');

        // //Footer sheet
        $departments = Department::query()->where('is_chart', 0)->whereHas('branch', function ($query) {
            $query->where('id', auth()->user()->branch_id);

        })->get();
        $groupDepartment = [];
        foreach ($departments as $department) {
            $result = [];
            $departmentIds = $department->descendants->pluck('id')->toArray();
            $employees = $this->data->whereIn('employee.department_id', $departmentIds)->all();
            $result['department'] = $department->name;
            $result['salary'] = 0;
            $result['total_amount'] = 0;
            $result['total_amount_tax'] = 0;
            $result['net_salary'] = 0;
            $result['code'] = '';
            foreach ($employees as $employee) {
                $result['salary'] += $employee->employee->currentSalary()->current_basic_salary;
                $result['code'] = $employee->employee->department->code ?? '';
                $result['total_amount'] += $employee->total_amount;
                $result['total_amount_tax'] += $employee->total_amount_tax;
                $result['net_salary'] += $employee->employee->current_basic_salary + $employee->total_amount - $employee->total_amount_tax;
            }
            $groupDepartment[$department->name] = $result;
        }
        // PERPARED BY:……………………
        $sheet->getCell('B'.($count + 10))->setValue('PERPARED BY:……………………');
        //APPROVED BY:………………………
        $sheet->getCell('D'.($count + 10))->setValue('APPROVED BY:……………………');
        // //PETROVIETNAM OIL LAO PETROLEUM DOMESTIC TRADING SOLE CO.,LTD. (PV OIL LAO TRADING)
        $sheet->mergeCells('B'.($count + 11).':H'.($count + 11));
        $sheet->getStyle('B'.($count + 11).':H'.($count + 11))->getAlignment()->setWrapText(true);
        $sheet->getCell('B'.($count + 11))->setValue('=B1');
        $sheet->getStyle('A'.($count + 11))->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('A'.($count + 11))->getFont()->setSize(11);
        $sheet->getStyle('A'.($count + 11))->getFont()->setBold(true);
        $sheet->getCell('B'.($count + 12))->setValue('=B2');
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
        $sheet->getCell('F'.($count + 15))->setValue('BONUS');
        $sheet->getCell('G'.($count + 15))->setValue('Retained Tax');
        $sheet->getCell('H'.($count + 15))->setValue('NET PAY');
        $sheet->getStyle('A'.($count + 15).':H'.($count + 15))->getFont()->setBold(true);
        $sheet->getStyle('A'.($count + 15).':H'.($count + 15))->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A'.($count + 15).':H'.($count + 15))->getAlignment()->setVertical('center');
        $sheet->getStyle('A'.($count + 15).':H'.($count + 15))->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.($count + 15).':H'.($count + 15 + count($groupDepartment)))->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('H'.($count + 15))
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
            $sheet->getCell('D'.($count + 16 + $key))->setValue($department['code']);

            $sheet->getCell('E'.($count + 16 + $key))->setValue($department['salary']);
            $sheet->getCell('E'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('F'.($count + 16 + $key))->setValue($department['total_amount']);
            $sheet->getCell('F'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('G'.($count + 16 + $key))->setValue($department['total_amount_tax']);
            $sheet->getCell('G'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('H'.($count + 16 + $key))->setValue('=F'.($count + 16 + $key).'-E'.($count + 16 + $key));
            $sheet->getCell('H'.($count + 16 + $key))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $key++;
        }

        // PERPARED BY:……………………
        $sheet->getCell('B'.($count + 18 + count($groupDepartment)))->setValue('PERPARED BY:……………………');
        // //APPROVED BY:………………………
        $sheet->getCell('D'.($count + 18 + count($groupDepartment)))->setValue('APPROVED BY:……………………');

        $sheet->getStyle('A1:T1000')->getFont()->setName('Times New Roman');
        $sheet->setShowGridlines(false);
    }

    public function registerEvents(): array
    {
        // $count = count($this->data);

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('4')->setRowHeight(72.75);
            },
        ];
    }

    public function title(): string
    {
        return 'BONUS';
    }
}
