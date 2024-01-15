<?php

namespace Modules\Employees\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Employees\Models\Employee;
use Modules\Payroll\Models\ExchangeRate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VietNamStaffExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $date;

    protected $branchName;

    public function __construct($data, $date, $branchName)
    {
        $this->data = $data->filter(function ($item) {
            return $item->employee->type == Employee::TYPE_EXPAT;
        });
        $this->date = $date;
        $this->branchName = $branchName;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 32,
            'C' => 15,
            'D' => 25,
            'E' => 12,
            'F' => 14,
            'G' => 14,
            'H' => 19,
            'I' => 19,
            'J' => 14,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 15,
            'O' => 15,
            'P' => 32,
            'Q' => 32,
        ];
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getCell('A1')->setValue($this->branchName);
        //Set color in A1
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getCell('A2')->setValue('SALATY '.$this->date);
        //Header row
        $sheet->getCell('A5')->setValue('No.');
        $sheet->getCell('B5')->setValue('NAME');
        $sheet->getCell('C5')->setValue('STAFF ID');
        $sheet->getCell('D5')->setValue('BANK ACCOUNT');
        $sheet->getCell('E5')->setValue('COST CENTER');
        $sheet->getCell('F5')->setValue('SALARY USD');
        $sheet->getCell('G5')->setValue('ADD DIFF '.$this->date);
        $sheet->getCell('H5')->setValue('ALLOWANCE USD');
        $sheet->getCell('I5')->setValue('TOTAL SL+ALLW USD');
        $sheet->getCell('J5')->setValue('OT AMOUNT LAK');
        $sheet->getCell('K5')->setValue('TOTAL SL+ALLW+OT LAK');
        $sheet->getCell('L5')->setValue('RETAINED TAX LAK');
        $sheet->getCell('M5')->setValue('NET PAY LAK');
        $sheet->getCell('N5')->setValue('RETAINED TAX USD');
        $sheet->getCell('O5')->setValue('NET PAY USD');
        $sheet->getCell('P5')->setValue('REMARK');
        $sheet->getCell('Q5')->setValue('SALARY 14.7%(RETIREMENT FUND) LAK');
        $sheet->getStyle('A5:Q5')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('C5D9F1');
        $sheet->getStyle('A5:Q5')->getFont()->setBold(true);
        $sheet->getStyle('A5:Q'.count($this->data) + 5)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A5:Q'.count($this->data) + 6)->getFont()->setSize(14);
        $sheet->getStyle('A5:Q5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:Q5')->getAlignment()->setVertical('center');
        $sheet->getCell('J4')->setValue('Exch Rate:');
        $exchangeRate = ExchangeRate::query()
            ->where('from_currency_code', 'USD')
            ->where('to_currency_code', 'LAK')
            ->where('created_at', '>=', Carbon::parse($this->date)->startOfMonth()->format('Y-m-d'))
            ->where('created_at', '<=', Carbon::parse($this->date)->endOfMonth()->format('Y-m-d'))
            ->first()
            ->rate;
        $sheet->getCell('K4')->setValue($exchangeRate ?? 0);
        $sheet->getCell('L4')->setValue('LAK/USD - ('.$this->date.')');
        $sheet->getCell('Q3')->setValue('=K4');
        $sheet->getCell('Q3')->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
        $sheet->getCell('Q4')->setValue('LAK/USD - ('.$this->date.')');
        $sheet->getStyle('Q4')->getFont()->setBold(true);
        //Binding data
        $no = 0;
        foreach ($this->data as $item) {
            $no++;
            $sheet->getCell('A'.($no + 5))->setValue($no);
            $sheet->getCell('B'.($no + 5))->setValue($item->employee->name);
            $sheet->getCell('C'.($no + 5))->setValue($item->employee->employee_code);
            $bankAccount = '';
            if ($item->employee->bankAccounts->count() > 0) {
                foreach ($item->employee->bankAccounts as $bank) {
                    if ($bank->bank_name == 'LDB') {
                        $bankAccount = $bank->account_number;
                    }
                }
            }
            $sheet->getCell('D'.($no + 5))->setValue($bankAccount ?? '');
            $sheet->getCell('E'.($no + 5))->setValue($item->employee->designation->code ?? '');
            $sheet->getCell('F'.($no + 5))->setValue($item->salary_json['basic_salary'] ?? 0);
            $sheet->getCell('F'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('G'.($no + 5))->setValue($item->salary_json['retaliation'] ?? 0);
            $sheet->getCell('G'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('H'.($no + 5))->setValue($item->salary_json['fixed_allowance'] ?? 0);
            $sheet->getCell('H'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('I'.($no + 5))->setValue('=F'.($no + 5).'+H'.($no + 5).'+G'.($no + 5));
            $sheet->getCell('I'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('J'.($no + 5))->setValue($item->salary_json['amount_ot'] ?? 0);
            $sheet->getCell('J'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('K'.($no + 5))->setValue('=I'.($no + 5).'*K4');
            $sheet->getCell('K'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('L'.($no + 5))->setValue($item->salary_json['personal_income_tax'] * $exchangeRate ?? 0);
            $sheet->getCell('L'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('M'.($no + 5))->setValue($item->net_salary * $exchangeRate ?? 0);
            $sheet->getCell('M'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('N'.($no + 5))->setValue($item->salary_json['personal_income_tax'] ?? 0);
            $sheet->getCell('N'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('O'.($no + 5))->setValue($item->net_salary ?? 0);
            $sheet->getCell('O'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            $sheet->getCell('P'.($no + 5))->setValue('');
            $sheet->getCell('Q'.($no + 5))->setValue('=(F'.($no + 5).'*$Q$3)*14.7%');
            $sheet->getCell('Q'.($no + 5))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
        }
        //Total row
        $sheet->getCell('F'.(count($this->data) + 6))->setValue('=SUM(F6:F'.(count($this->data) + 5).')');
        $sheet->getCell('F'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('G'.(count($this->data) + 6))->setValue('=SUM(G6:G'.(count($this->data) + 5).')');
        $sheet->getCell('G'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('H'.(count($this->data) + 6))->setValue('=SUM(H6:H'.(count($this->data) + 5).')');
        $sheet->getCell('H'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('I'.(count($this->data) + 6))->setValue('=SUM(I6:I'.(count($this->data) + 5).')');
        $sheet->getCell('I'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('J'.(count($this->data) + 6))->setValue('=SUM(J6:J'.(count($this->data) + 5).')');
        $sheet->getCell('J'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('K'.(count($this->data) + 6))->setValue('=SUM(K6:K'.(count($this->data) + 5).')');
        $sheet->getCell('K'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('L'.(count($this->data) + 6))->setValue('=SUM(L6:L'.(count($this->data) + 5).')');
        $sheet->getCell('L'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('M'.(count($this->data) + 6))->setValue('=SUM(M6:M'.(count($this->data) + 5).')');
        $sheet->getCell('M'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('N'.(count($this->data) + 6))->setValue('=SUM(N6:N'.(count($this->data) + 5).')');
        $sheet->getCell('N'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
        $sheet->getCell('O'.(count($this->data) + 6))->setValue('=SUM(O6:O'.(count($this->data) + 5).')');
        $sheet->getCell('O'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
        $sheet->getCell('Q'.(count($this->data) + 6))->setValue('=SUM(Q6:Q'.(count($this->data) + 5).')');
        $sheet->getCell('Q'.(count($this->data) + 6))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

        $sheet->getStyle('F'.$no + 6 .':Q'.$no + 6)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('F'.$no + 6 .':Q'.$no + 6)->getBorders()->getAllBorders()->setBorderStyle('double');
        $sheet->getStyle('F'.$no + 6 .':Q'.$no + 6)->getFont()->setBold(true);

        // PERPARED BY:……………………
        $sheet->getCell('A'.(count($this->data) + 10))->setValue('PERPARED BY:……………………');
        $sheet->getStyle('A'.(count($this->data) + 10))->getFont()->setBold(true);
        //APPROVED BY:………………………
        $sheet->getCell('F'.(count($this->data) + 10))->setValue('APPROVED BY:……………………');
        $sheet->getStyle('F'.(count($this->data) + 10))->getFont()->setBold(true);

        $sheet->getStyle('A5:Q'.$no + 5)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->setShowGridlines(false);
    }

    public function registerEvents(): array
    {
        $count = count($this->data);

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('5')->setRowHeight(62);

            },
        ];
    }

    public function title(): string
    {
        return 'VIETNAM STAFF';
    }
}
