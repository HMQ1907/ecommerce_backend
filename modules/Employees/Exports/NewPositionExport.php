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
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewPositionExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $branchName;

    public function __construct($data, $branchName)
    {
        $this->data = $data;
        $this->branchName = $branchName;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 45,
            'C' => 20,
            'D' => 16,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 9,
            'I' => 20,
            'J' => 20,
        ];
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:J1');
        $sheet->getCell('A1')->setValue($this->branchName);
        //Header row
        $sheet->getCell('A3')->setValue('No.');
        $sheet->getCell('B3')->setValue('NAME');
        $sheet->getCell('C3')->setValue('BANK ACCOUNT');
        $sheet->getCell('D3')->setValue('Apply Salary Date');
        $sheet->getCell('E3')->setValue('Previous Salary');
        $sheet->getCell('F3')->setValue('Salary Staff');
        $sheet->getCell('G3')->setValue('Increment Date');
        $sheet->getCell('H3')->setValue('Months');
        $sheet->getCell('I3')->setValue('Amount');
        $sheet->getCell('J3')->setValue('Amount of money');
        $sheet->getStyle('A3:J3')->getFont()->setBold(true);
        $sheet->getStyle('A3:J3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A3:J3')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:J3')->getAlignment()->setVertical('center');
        $sheet->getStyle('A3:J'.count($this->data) + 4)->getBorders()->getAllBorders()->setBorderStyle('thin');
        //Binding data
        $no = 0;
        foreach ($this->data as $key => $item) {
            $no++;
            $sheet->getCell('A'.($no + 3))->setValue($no);
            $sheet->getStyle('A'.($no + 3))->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
            $sheet->getCell('B'.($no + 3))->setValue($item->employee->full_name ?? '');
            $sheet->getCell('C'.($no + 3))->setValue($item->employee->bankAccounts->first()->account_number ?? '');

            $sheet->getCell('D'.($no + 3))->setValue(
                Carbon::parse($item->apply_salary_date)->format('d/m/Y')
            );
            $sheet->getStyle('D'.($no + 3))->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
            $sheet->getCell('E'.($no + 3))->setValue($item->previous_salary);
            $sheet->getCell('E'.($no + 3))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('F'.($no + 3))->setValue($item->new_salary);
            $sheet->getCell('F'.($no + 3))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getStyle('F'.($no + 3))->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

            $type = $item->employee->type ?? '';
            $sheet->getCell('G'.($no + 3))->setValue($type.' '.Carbon::parse($item->increment_date)->format('d/m/Y'));
            $sheet->getStyle('G'.($no + 3))->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

            $sheet->getCell('H'.($no + 3))->setValue($item->months);
            $sheet->getStyle('H'.($no + 3))->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

            $sheet->getCell('I'.($no + 3))->setValue($item->amount);
            $sheet->getCell('I'.($no + 3))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $workingDays = Carbon::parse($item->increment_date)->diffInDaysFiltered(function (Carbon $date) {
                return $date->isWeekday();
            }, Carbon::parse($item->increment_date)->endOfMonth()) - 1;

            $amountOfMoney = round(($item->amount * $item->months - ($item->amount / 22 * $workingDays))) ?? 0;
            $sheet->getCell('J'.($no + 3))->setValue($amountOfMoney);
            $sheet->getCell('J'.($no + 3))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        }

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
        return 'ໄລ່ເງີນເດືອນຄືນຫຼັງ';
    }
}
