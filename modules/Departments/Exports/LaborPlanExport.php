<?php

namespace Modules\Departments\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaborPlanExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles
{
    use Exportable;

    public function columnWidths(): array
    {
        return [
            'B' => 40,
            'C' => 15,
            'E' => 15,
            'H' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:H6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:H7')->getAlignment()->setVertical('center');
        $sheet->getStyle('A6:H7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A8:A100')->getAlignment()->setVertical('center');
        $sheet->getStyle('A8:A100')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:H1')->getFont()->setSize(13)->setBold(true);
        $sheet->getStyle('A2:H2')->getFont()->setSize(13)->setBold(true);
        $sheet->getStyle('A4:H6')->getFont()->setSize(15)->setBold(true);
        $sheet->getStyle('A6:H32')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A8:H20')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:H100')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A6:H100')->getFont()->setSize(12);
        $sheet->getStyle('A1:H8')->getFont()->setBold(true);

        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A4:H4');
        $sheet->setCellValue('A1', 'TỔNG CÔNG TY DẦU VIỆT NAM - CTCP (PVOIL)');
        $sheet->setCellValue('A2', 'ĐƠN VỊ:…………………………………');
        $sheet->setCellValue('A4', 'KẾ HOẠCH LAO ĐỘNG NĂM 2023');

        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'TT');

        $sheet->mergeCells('B6:B7');
        $sheet->setCellValue('B6', 'Chức danh công việc');

        $sheet->mergeCells('C6:C7');
        $sheet->setCellValue('C6', 'Số lao động có mặt ngày 31/12 năm 2022');

        $sheet->mergeCells('D6:E6');
        $sheet->setCellValue('D6', 'Số lao động kế hoạch năm 2023');

        $sheet->setCellValue('D7', 'Định biên');
        $sheet->setCellValue('E7', 'Bình quân kế hoạch');

        $sheet->mergeCells('F6:G6');
        $sheet->setCellValue('F6', 'KH tăng/giảm trong năm 2023');
        $sheet->setCellValue('F7', 'Tăng');
        $sheet->setCellValue('G7', 'Giảm');

        $sheet->mergeCells('H6:H7');
        $sheet->setCellValue('H6', 'Giải trình rõ lý do tăng/giảm lao động');

        $sheet->setCellValue('A8', 'I');
        $sheet->setCellValue('A9', '1');
        $sheet->setCellValue('A10', '2');
        $sheet->setCellValue('A11', '3');
        $sheet->setCellValue('A12', '4');
        $sheet->setCellValue('A13', 'II');
        $sheet->setCellValue('A14', '1');
        $sheet->setCellValue('A15', '1,1');
        $sheet->setCellValue('A16', '1,2');
        $sheet->setCellValue('A17', '2');
        $sheet->setCellValue('A18', '2,1');
        $sheet->setCellValue('A19', '2,2');
        $sheet->setCellValue('A20', '3');
        $sheet->setCellValue('A21', '3,1');
        $sheet->setCellValue('A22', '3,2');
        $sheet->setCellValue('A23', '4');
        $sheet->setCellValue('A24', '4,1');
        $sheet->setCellValue('A25', '4,2');
        $sheet->setCellValue('A26', '...');
        $sheet->setCellValue('A27', 'III');
        $sheet->setCellValue('A28', '1');
        $sheet->setCellValue('A29', '2');
        $sheet->setCellValue('A30', '');
        $sheet->setCellValue('A31', '3');

        $sheet->getStyle('A1:O100')->getFont()->setName('Times New Roman');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('7')->setRowHeight(50);
            },
        ];
    }
}
