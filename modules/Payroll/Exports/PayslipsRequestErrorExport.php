<?php

namespace Modules\Payroll\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayslipsRequestErrorExport implements FromArray, ShouldAutoSize, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles
{
    use Exportable;

    protected $data;

    protected $failures;

    protected $headers = [
        'employee_code' => 1,
        'amount_ot' => 2,
        'advance' => 3,
        'actual_working_days' => 4,
        'hrs' => 5,
    ];

    public function __construct($data, $failures)
    {
        $this->data = $data;
        $this->failures = $failures;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return array_keys($this->headers);
    }

    public function map($row): array
    {
        return [
            $row['employee_code'],
            $row['amount_ot'],
            $row['advance'],
            $row['actual_working_days'],
            $row['hrs'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        foreach ($this->failures as $row => $col) {
            foreach (array_keys($col) as $key) {
                $sheet->getCell([$this->headers[$key], $row + 2])->getStyle()->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()
                    ->setRGB('yellow');
            }
        }
    }
}
