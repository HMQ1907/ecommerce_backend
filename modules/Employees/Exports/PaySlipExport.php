<?php

namespace Modules\Employees\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaySlipExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $date;

    protected $branchName;

    public function __construct($data, $date, $branchName)
    {
        $this->data = $data;
        $this->date = $date;
        $this->branchName = $branchName;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 30,
            'C' => 18,
            'D' => 30,
            'E' => 18,
            'G' => 35,
        ];
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getCell('A2')->setValue($this->branchName);
        $sheet->getCell('A3')->setValue('PAY SLIP');
        $sheet->getCell('D3')->setValue('Pay Period:');
        $sheet->getCell('D4')->setValue('Pay day:');
        $sheet->getCell('E3')->setValue('Dec-22');
        $sheet->getCell('E4')->setValue('27-Dec-2022');
        $sheet->getCell('G2')->setValue('27 December 2022');
        $sheet->getStyle('G2')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF00');
        $sheet->getStyle('G3')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FDE9D9');
        foreach ($this->data as $key => $item) {
            if ($key % 2 == 0) {
                $sheet->getStyle('A'. 5 + 19 * $key.':E'. 25 + 20 * $key)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FDE9D9');
            } else {
                $sheet->getStyle('A'. 5 + 19 * $key.':E'. 25 + 20 * $key)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF00');
            }

            $sheet->getCell('A'. 6 + 20 * $key - $key)->setValue($this->branchName);
            $sheet->getCell('A'. 7 + 20 * $key - $key)->setValue('PAY SLIP');
            //Border button 'A' . 7 + 20 * $key - $key
            $sheet->getStyle('A'. 7 + 20 * $key - $key)->getBorders()->getBottom()->setBorderStyle('thin');
            $sheet->getCell('D'. 7 + 20 * $key - $key)->setValue('Pay Period:');
            $sheet->getCell('D'. 8 + 20 * $key - $key)->setValue('Pay day:');
            $sheet->getCell('E'. 7 + 20 * $key - $key)->setValue('Dec-22');
            $sheet->getCell('E'. 8 + 20 * $key - $key)->setValue('27-Dec-2022');
            $sheet->getCell('A'. 9 + 20 * $key - $key)->setValue('Name:');
            $sheet->getStyle('A'. 9 + 20 * $key - $key)->getFont()->setBold(true);
            $sheet->getCell('B'. 9 + 20 * $key - $key)->setValue($item->employee->name);
            //Set color text 'A' . 9 + 20 * $key - $key
            $sheet->getStyle('A'. 9 + 20 * $key - $key)->getFont()->getColor()->setARGB('FF0000');
            //Set color text 'B' . 9 + 20 * $key - $key
            $sheet->getStyle('B'. 9 + 20 * $key - $key)->getFont()->getColor()->setARGB('FF0000');
            $sheet->getCell('A'. 10 + 20 * $key - $key)->setValue('Cost Center');
            $sheet->getCell('B'. 10 + 20 * $key - $key)->setValue('-----');

            $sheet->mergeCells('A'. 11 + 20 * $key - $key.':B'. 11 + 20 * $key - $key);
            $sheet->getCell('A'. 11 + 20 * $key - $key)->setValue("BANK'S ACCOUNT:");
            $sheet->mergeCells('C'. 11 + 20 * $key - $key.':E'. 11 + 20 * $key - $key);
            $sheet->getCell('C'. 11 + 20 * $key - $key)->setValue($item->employee->bankAccounts->count() > 0 ? $item->employee->bankAccounts[count($item->employee->bankAccounts) - 1]->account_number : '');
            $sheet->getStyle('A'. 7 + 20 * $key - $key.':E'. 11 + 20 * $key - $key)->getBorders()->getBottom()->setBorderStyle('double');
            $sheet->getStyle('A'. 12 + 20 * $key - $key)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'. 12 + 20 * $key - $key)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('C'. 12 + 20 * $key - $key)->getAlignment()->setVertical('center');
            $sheet->getStyle('C'. 12 + 20 * $key - $key)->getAlignment()->setHorizontal('center');
            $sheet->getCell('A'. 13 + 20 * $key - $key)->setValue('Salary');
            $sheet->getCell('E'. 13 + 20 * $key - $key)->setValue($item->net_salary);
            $sheet->getStyle('E'. 13 + 20 * $key - $key)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('A'. 14 + 20 * $key - $key)->setValue(' Social Security');
            $sheet->getCell('E'. 14 + 20 * $key - $key)->setValue($item->salary_json['social_security']);
            $sheet->getStyle('E'. 14 + 20 * $key - $key)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('A'. 15 + 20 * $key - $key)->setValue('  Position & Housing Allow.');
            $sheet->getCell('E'. 15 + 20 * $key - $key)->setValue($item->salary_json['fixed_allowance']);
            $sheet->getStyle('E'. 15 + 20 * $key - $key)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('A'. 16 + 20 * $key - $key)->setValue(' Overtime');
            $sheet->getCell('C'. 16 + 20 * $key - $key)->setValue('HRS.');
            $sheet->getCell('D'. 16 + 20 * $key - $key)->setValue($item->salary_json['ot'] ?? 0);
            $sheet->getCell('E'. 16 + 20 * $key - $key)->setValue($item->salary_json['amount_ot']);
            $sheet->getStyle('E'. 16 + 20 * $key - $key)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('A'. 18 + 20 * $key - $key)->setValue('Total');
            $sheet->getCell('E'. 18 + 20 * $key - $key)->setValue($item->gross_salary);
            $sheet->getStyle('E'. 18 + 20 * $key - $key)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('A'. 19 + 20 * $key - $key)->setValue(' (Reained TAX)');
            $sheet->getCell('E'. 19 + 20 * $key - $key)->setValue($item->salary_json['personal_income_tax']);
            $sheet->getStyle('E'. 19 + 20 * $key - $key)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('A'. 20 + 20 * $key - $key)->setValue('Total');
            $sheet->getStyle('A'. 20 + 20 * $key - $key)->getBorders()->getBottom()->setBorderStyle('thin');
            $sheet->getStyle('E'. 20 + 20 * $key - $key)->getBorders()->getBottom()->setBorderStyle('thin');
            $sheet->getCell('E'. 20 + 20 * $key - $key)->setValue($item->net_salary);
            $sheet->getStyle('E'. 20 + 20 * $key - $key)->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('C'. 21 + 20 * $key - $key)->setValue('Date:');
            $sheet->getCell('C'. 22 + 20 * $key - $key)->setValue('Sign:');
            $sheet->getStyle('D'. 21 + 20 * $key - $key)->getBorders()->getBottom()->setBorderStyle('dotted');
            $sheet->getStyle('D'. 22 + 20 * $key - $key)->getBorders()->getBottom()->setBorderStyle('dotted');
            $sheet->getStyle('A'. 23 + 20 * $key - $key.':E'. 23 + 20 * $key - $key)->getBorders()->getBottom()->setBorderStyle('dashDotDot');

            $sheet->getStyle('A1:T1000')->getFont()->setName('Times New Roman');
            $sheet->setShowGridlines(false);
        }
    }

    public function registerEvents(): array
    {
        $count = count($this->data);

        return [
            AfterSheet::class => function (AfterSheet $event) {
                // $event->sheet->getDelegate()->getRowDimension('9')->setRowHeight(30);
            },
        ];
    }

    public function title(): string
    {
        return 'PAY SLIP';
    }
}
