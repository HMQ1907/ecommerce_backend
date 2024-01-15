<?php

namespace Modules\Attendances\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromView, WithColumnWidths, WithEvents, WithStyles
{
    use Exportable;

    protected $items;

    protected $startDate;

    protected $endDate;

    protected $dayInMonth;

    public function __construct($items, $startDate, $endDate)
    {
        $this->items = $items;
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = $endDate;
        $this->dayInMonth = Carbon::parse($this->startDate)->daysInMonth;
    }

    public function view(): View
    {
        return view('attendances::exports.attendance', [
            'items' => $this->items,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'dayInMonth' => $this->dayInMonth,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 20,
            'C' => 28,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $countSize = count($this->items);
        $range = 65 + 7 + $this->dayInMonth;
        $resetChar = 0;
        if ($range > 90) {
            $resetChar = $range - 90;
        }
        $sheet->getStyle('A7:A'.chr($resetChar + 65).'8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('D9:A'.chr($resetChar + 65).$countSize * 6 + 8)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $horizolRange = 'A'.($countSize + 7).':Z'.($countSize + 7);
        $sheet->getStyle($horizolRange)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('A1:A'.chr($resetChar + 65).'8')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        $sheet->freezePane('A9');
        $sheet->freezePane('E9');
        $sheet->mergeCells('A7:A8');
        $sheet->mergeCells('B7:B8');
        $sheet->mergeCells('C7:C8');

        $sheet->getStyle('A1:A'.chr($resetChar + 65).'8')->getFont()->setSize(10)->setName('Microsoft Sans Serif');
    }

    public function registerEvents(): array
    {
        $countSize = count($this->items);

        return [
            AfterSheet::class => function (AfterSheet $event) use ($countSize) {
                $worksheet = $event->sheet->getDelegate();
                $startColumnNumber = 5;
                $endColumnNumber = $this->dayInMonth + 4;

                for ($colNumber = $startColumnNumber; $colNumber <= $endColumnNumber; $colNumber++) {
                    if ($colNumber > 25) {
                        $col = chr(64 + floor($colNumber / 26)).chr(65 + ($colNumber % 26));
                    } else {
                        $col = chr(64 + $colNumber);
                    }

                    $cellValue = $worksheet->getCell($col.'8')->getValue();

                    if ($cellValue === 'CN') {
                        $columnRange = $col.'7'.':'.$col.($countSize * 6 + 8);
                        $worksheet->getStyle($columnRange)
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('EFC2F0');
                    }
                }
            },
        ];
    }
}
