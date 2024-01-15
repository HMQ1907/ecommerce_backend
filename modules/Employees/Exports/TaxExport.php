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

class TaxExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
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
            'A' => 7,
            'B' => 45,
            'C' => 20,
            'D' => 20,
            'E' => 20,
        ];
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getCell('A4')->setValue('Ministry of Finance');
        $sheet->getCell('A5')->setValue('Excise Department ');
        $sheet->getCell('A6')->setValue($this->branchName);
        $sheet->getCell('A7')->setValue('Vientiane 143, Sithane Raod P.O.Box 153 Tel.(021) 214142, 212842 . Fax. (021) 215605');
        $sheet->getCell('F2')->setValue("Laos People's Democratic Republic");
        $sheet->getCell('E3')->setValue('Peace Independence Democraty Unity and Prosperity');
        $sheet->getCell('F4')->setValue('------- === **** === -------');
        $sheet->getCell('F5')->setValue('INCOME TAX OF EMPLOYEES');
        $sheet->getCell('F6')->setValue('PERIOD:SALARY-'.$this->date);
        //Header row
        $sheet->getCell('A9')->setValue('No.');
        $sheet->getCell('B9')->setValue('Name');
        $sheet->getCell('C9')->setValue('TOTAL Benefits');
        $sheet->getCell('D9')->setValue('Examption under');
        $sheet->getCell('E9')->setValue('TAX to be Paid');
        $sheet->getStyle('A9:E9')->getFont()->setBold(true);
        $sheet->getStyle('A9:E9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A9:E9')->getAlignment()->setVertical('center');
        $sheet->getStyle('A9:E'.count($this->data) + 9)->getBorders()->getAllBorders()->setBorderStyle('thin');
        //Binding data
        foreach ($this->data as $key => $item) {
            $sheet->getCell('A'.($key + 10))->setValue($key + 1);
            $sheet->getCell('A'.($key + 10))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('B'.($key + 10))->setValue($item->employee->name);
            $sheet->getCell('C'.($key + 10))->setValue($item->gross_salary);
            $sheet->getCell('C'.($key + 10))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('D'.($key + 10))->setValue('-');
            $sheet->getCell('E'.($key + 10))->setValue($item->salary_json['personal_income_tax']);
            $sheet->getCell('E'.($key + 10))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        }
        $sheet->getCell('B'.(count($this->data) + 10))->setValue('Total');
        $sheet->getStyle('B'.(count($this->data) + 10).':E'.(count($this->data) + 10))->getFont()->setBold(true);
        $sheet->getStyle('B'.(count($this->data) + 10).':E'.(count($this->data) + 10))->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getCell('C'.(count($this->data) + 10))->setValue('=SUM(C10:C'.(count($this->data) + 9).')');
        $sheet->getCell('C'.(count($this->data) + 10))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('E'.(count($this->data) + 10))->setValue('=SUM(E10:E'.(count($this->data) + 9).')');
        $sheet->getCell('E'.(count($this->data) + 10))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

        // PERPARED BY:……………………
        $sheet->getCell('B'.(count($this->data) + 14))->setValue('PERPARED BY:……………………');
        $sheet->getStyle('B'.(count($this->data) + 14))->getFont()->setBold(true);
        //APPROVED BY:………………………
        $sheet->getCell('D'.(count($this->data) + 14))->setValue('APPROVED BY:……………………');
        $sheet->getStyle('D'.(count($this->data) + 14))->getFont()->setBold(true);

        $sheet->getStyle('A1:T1000')->getFont()->setName('Times New Roman');
        $sheet->setShowGridlines(false);
    }

    public function registerEvents(): array
    {
        $count = count($this->data);

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('9')->setRowHeight(30);
            },
        ];
    }

    public function title(): string
    {
        return 'TAX';
    }
}
