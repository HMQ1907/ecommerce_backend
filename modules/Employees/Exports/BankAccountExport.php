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

class BankAccountExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $date;

    protected $branchName;

    public function __construct($data, $date, $branchName)
    {
        $this->data = $data->filter(function ($item) {
            return $item->employee->bankAccounts->count() > 0;
        });
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
        $sheet->getCell('B1')->setValue($this->branchName);
        $sheet->mergeCells('B2:D2');
        $sheet->getCell('B2')->setValue('PERIOD:'.$this->date);
        //Header row
        $sheet->getCell('A4')->setValue('No.');
        $sheet->getCell('B4')->setValue('NAME');
        $sheet->getCell('C4')->setValue('BANK ACCOUNT');
        $sheet->getCell('D4')->setValue('NET PAY');
        $sheet->getStyle('A4:D4')->getFont()->setBold(true);
        $sheet->getStyle('A4:D4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:D4')->getAlignment()->setVertical('center');
        $sheet->getStyle('A4:D'.count($this->data) + 5)->getBorders()->getAllBorders()->setBorderStyle('thin');
        //Binding data
        $no = 0;
        foreach ($this->data as $key => $item) {
            $no++;
            $sheet->getCell('A'.($no + 4))->setValue($no);
            $sheet->getCell('B'.($no + 4))->setValue($item->employee->name);
            $sheet->getCell('C'.($no + 4))->setValue($item->employee->bankAccounts->count() > 0 ? $item->employee->bankAccounts[count($item->employee->bankAccounts) - 1]->account_number : '');
            $sheet->getCell('D'.($no + 4))->setValue($item->net_salary);
            $sheet->getCell('D'.($no + 4))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        }
        //Total
        $sheet->getCell('A'.(count($this->data) + 5))->setValue('TOTAL');
        $sheet->getCell('D'.(count($this->data) + 5))->setValue('=SUM(D5:D'.(count($this->data) + 4).')');
        $sheet->getCell('D'.(count($this->data) + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        //Set bold
        $sheet->getStyle('A'.(count($this->data) + 5).':D'.(count($this->data) + 5))->getFont()->setBold(true);
        //APPROVED BY:………………………
        $sheet->getCell('B'.(count($this->data) + 8))->setValue('APPROVED BY:……………………');
        $sheet->getStyle('B'.(count($this->data) + 8))->getFont()->setBold(true);

        $sheet->getStyle('A1:T1000')->getFont()->setName('Times New Roman');
        $sheet->setShowGridlines(false);
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
        return 'LAO STAFF BANK ACCOUNT';
    }
}
