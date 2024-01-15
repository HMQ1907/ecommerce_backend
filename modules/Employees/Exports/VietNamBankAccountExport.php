<?php

namespace Modules\Employees\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Employees\Models\Employee;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VietNamBankAccountExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $date;

    protected $branchName;

    public function __construct($data, $date, $branchName)
    {
        $this->data = $data->filter(function ($item) {
            if ($item->employee->bankAccounts->count() > 0) {
                foreach ($item->employee->bankAccounts as $bankAccount) {
                    return $bankAccount->bank_name == 'LDB' && $item->employee->type == Employee::TYPE_EXPAT;
                }
            }

            return null;
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
        $sheet->mergeCells('B1:E1');
        $sheet->getCell('B1')->setValue($this->branchName);
        $sheet->getCell('B1')->getStyle()->getFont()->setBold(true);
        $sheet->getCell('B1')->getStyle()->getFont()->setSize(14);
        $sheet->getCell('B1')->getStyle()->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('B2:E2');
        $sheet->getCell('B2')->setValue('SALARY:'.$this->date);
        $sheet->getCell('B2')->getStyle()->getFont()->setBold(true);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal('center');
        //Header row
        $sheet->getCell('A4')->setValue('No.');
        $sheet->getCell('B4')->setValue('NAME');
        $sheet->getCell('C4')->setValue('STAFF ID');
        $sheet->getCell('D4')->setValue('BANK ACCOUNT');
        $sheet->getCell('E4')->setValue('NET PAY');
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        $sheet->getStyle('A4:E4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:E4')->getAlignment()->setVertical('center');
        $sheet->getStyle('A4:E'.count($this->data) + 5)->getBorders()->getAllBorders()->setBorderStyle('thin');
        //Binding data
        $no = 0;
        foreach ($this->data as $key => $item) {
            $no++;
            $sheet->getCell('A'.($no + 4))->setValue($no);
            $sheet->getCell('B'.($no + 4))->setValue($item->employee->name);
            $sheet->getCell('C'.($no + 4))->setValue($item->employee->employee_code);
            $bankAccount = '';
            if ($item->employee->bankAccounts->count() > 0) {
                foreach ($item->employee->bankAccounts as $bank) {
                    if ($bank->bank_name == 'LDB') {
                        $bankAccount = $bank->account_number;
                    }
                }
            }
            $sheet->getCell('D'.($no + 4))->setValue($bankAccount ?? '');
            $sheet->getCell('E'.($no + 4))->setValue($item->net_salary);
            $sheet->getCell('E'.($no + 4))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        }
        //Total
        $sheet->getCell('A'.(count($this->data) + 5))->setValue('TOTAL');
        $sheet->getCell('E'.(count($this->data) + 5))->setValue('=SUM(E5:E'.(count($this->data) + 4).')');
        $sheet->getCell('E'.(count($this->data) + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        //Set bold
        $sheet->getStyle('A'.(count($this->data) + 5).':E'.(count($this->data) + 5))->getFont()->setBold(true);
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
        return 'VIET NAM STAFF BANK ACCOUNT';
    }
}
