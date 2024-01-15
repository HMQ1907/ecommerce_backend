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

class OverTimeExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $date;

    public function __construct($data, $date)
    {
        $this->data = $data;
        $this->date = $date;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 45,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
        ];
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A3:A4');
        $sheet->mergeCells('B3:B4');
        $sheet->mergeCells('C3:C4');
        $sheet->mergeCells('D3:D4');
        $sheet->getCell('B1')->setValue('OVERTIME  STAFF');
        $sheet->getCell('D1')->setValue('OVERTIME  STAFF');
        $sheet->getStyle('D1')->getFont()->setBold(true);
        $sheet->getCell('B2')->setValue('PERIOD:SALARY-'.$this->date);
        //Header row
        $sheet->getCell('A3')->setValue('No.');
        $sheet->getCell('B3')->setValue('Name');
        $sheet->getCell('C3')->setValue('SALARY');
        $sheet->getCell('D3')->setValue('Rate/Hour (Salary/'.$this->date.')');
        $sheet->mergeCells('E3:F3');
        $sheet->getCell('E3')->setValue('Total OverTime');
        $sheet->getCell('E4')->setValue('HRS.');
        $sheet->getCell('F4')->setValue('AMOUNT');
        $sheet->getStyle('A3:F4')->getFont()->setBold(true);
        $sheet->getStyle('A3:F4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:F4')->getAlignment()->setVertical('center');
        $sheet->getStyle('A3:F'.count($this->data) + 5)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A3:F'.count($this->data) + 4)->getAlignment()->setWrapText(true);

        //Binding data
        foreach ($this->data as $key => $item) {
            $sheet->getCell('A'.($key + 5))->setValue($key + 1);
            $sheet->getCell('A'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('B'.($key + 5))->setValue($item->employee->name);
            $sheet->getCell('C'.($key + 5))->setValue($item->net_salary);
            $sheet->getCell('C'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('D'.($key + 5))->setValue('-');
            $sheet->getCell('E'.($key + 5))->setValue($item->salary_json['ot'] ?? 0);
            $sheet->getCell('F'.($key + 5))->setValue($item->salary_json['personal_income_tax']);
            $sheet->getCell('F'.($key + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        }
        $sheet->getCell('B'.(count($this->data) + 5))->setValue('Total');
        $sheet->getStyle('B'.(count($this->data) + 5).':F'.(count($this->data) + 5))->getFont()->setBold(true);
        $sheet->getCell('C'.(count($this->data) + 5))->setValue('=SUM(C5:C'.(count($this->data) + 4).')');
        $sheet->getCell('C'.(count($this->data) + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('E'.(count($this->data) + 5))->setValue('=SUM(E5:E'.(count($this->data) + 4).')');
        $sheet->getCell('E'.(count($this->data) + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('F'.(count($this->data) + 5))->setValue('=SUM(F5:F'.(count($this->data) + 4).')');
        $sheet->getCell('F'.(count($this->data) + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

        // PERPARED BY:……………………
        $sheet->getCell('B'.(count($this->data) + 10))->setValue('PERPARED BY:……………………');
        $sheet->getStyle('B'.(count($this->data) + 10))->getFont()->setBold(true);
        //APPROVED BY:………………………
        $sheet->getCell('E'.(count($this->data) + 10))->setValue('APPROVED BY:……………………');
        $sheet->getStyle('E'.(count($this->data) + 10))->getFont()->setBold(true);

        $sheet->getStyle('A1:T1000')->getFont()->setName('Times New Roman');
        $sheet->setShowGridlines(false);
    }

    public function registerEvents(): array
    {
        $count = count($this->data);

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('3')->setRowHeight(30);
                //Hide column D
                $event->sheet->getColumnDimension('D')->setVisible(false);
            },
        ];
    }

    public function title(): string
    {
        return 'O.T';
    }
}
