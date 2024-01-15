<?php

namespace Modules\Departments\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryDepartmentExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles
{
    use Exportable;

    public function columnWidths(): array
    {
        return [
            'B' => 40,
            'H' => 20,
            'I' => 20,
            'M' => 20,
            'N' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:O2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:O6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:O6')->getAlignment()->setVertical('center');
        $sheet->getStyle('A8:O20')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A8:O20')->getAlignment()->setVertical('center');
        $sheet->getStyle('A11:O12')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A11:O12')->getAlignment()->setVertical('center');
        $sheet->getStyle('B13:B20')->getAlignment()->setHorizontal('left');

        $sheet->getStyle('A8:O20')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A1:O6')->getFont()->setBold(true);
        $sheet->getStyle('A8:O20')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:O6')->getFont()->setSize(12)->setBold(true);
        $sheet->getStyle('A4:O10')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A11:O12')->getFont()->setBold(true);
        $sheet->getStyle('A20:O20')->getFont()->setBold(true);

        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');
        $sheet->setCellValue('A1', 'TỔNG CÔNG TY DẦU VIỆT NAM - CTCP (PVOIL)');
        $sheet->setCellValue('A2', 'ĐƠN VỊ:…………………………………');
        $sheet->setCellValue('A4', 'TÌNH HÌNH LAO ĐỘNG - TIỀN LƯƠNG ƯỚC THỰC HIỆN NĂM 2022 VÀ KẾ HOẠCH  NĂM 2023');

        $sheet->mergeCells('A8:A10');
        $sheet->setCellValue('A8', 'TT');

        $sheet->mergeCells('B8:B10');
        $sheet->setCellValue('B8', 'Bộ phận');

        $sheet->mergeCells('C8:I8');
        $sheet->setCellValue('C8', 'Ước Thực hiện năm 2022');

        $sheet->mergeCells('C9:C10');
        $sheet->setCellValue('C9', 'Lao động ước tính đến 31/12');

        $sheet->mergeCells('D9:D10');
        $sheet->setCellValue('D9', 'Lao động TTSD bình quân');

        $sheet->mergeCells('E9:F9');
        $sheet->setCellValue('E9', 'Quỹ tiền lương thực chi ước đến 31/12');

        $sheet->setCellValue('E10', 'Chi từ quỹ lương năm trước chuyển sang');
        $sheet->setCellValue('F10', 'Chi từ quỹ lương năm 2022');

        $sheet->mergeCells('G9:G10');
        $sheet->setCellValue('G9', 'Quỹ tiền lương trích');
        $sheet->mergeCells('H9:H10');
        $sheet->setCellValue('H9', 'Quỹ Khen thưởng - Phúc lợi - quỹ Khen thưởng BĐH');
        $sheet->mergeCells('I9:I10');
        $sheet->setCellValue('I9', 'Thu nhập khác (Thêm giờ, Ăn ca, bồi dưỡng độc hại, Thưởng an toàn…)');
        $sheet->mergeCells('J8:N8');
        $sheet->setCellValue('J8', 'Kế hoạch 2023');
        $sheet->mergeCells('J9:J10');
        $sheet->setCellValue('J9', 'Lao động định mức');
        $sheet->mergeCells('K9:K10');
        $sheet->setCellValue('K9', 'Lao động bình quân kế hoạch');
        $sheet->mergeCells('L9:L10');
        $sheet->setCellValue('L9', 'Quỹ tiền lương trích');
        $sheet->mergeCells('M9:M10');
        $sheet->setCellValue('M9', 'Quỹ Khen thưởng - Phúc lợi - quỹ Khen thưởng BĐH');
        $sheet->mergeCells('N9:N10');
        $sheet->setCellValue('N9', 'Thu nhập khác (Thêm giờ, Ăn ca, bồi dưỡng độc hại, Thưởng an toàn…)');
        $sheet->mergeCells('O8:O10');
        $sheet->setCellValue('O8', 'Ghi chú');

        $sheet->setCellValue('C11', 'người');
        $sheet->setCellValue('D11', 'người');
        $sheet->setCellValue('E11', 'tr.đồng');
        $sheet->setCellValue('F11', 'tr.đồng');
        $sheet->setCellValue('G11', 'tr.đồng');
        $sheet->setCellValue('H11', 'tr.đồng');
        $sheet->setCellValue('I11', 'tr.đồng');
        $sheet->setCellValue('J11', 'người');
        $sheet->setCellValue('K11', 'người');
        $sheet->setCellValue('L11', 'tr.đồng');
        $sheet->setCellValue('M11', 'tr.đồng');
        $sheet->setCellValue('N11', 'tr.đồng');

        $sheet->setCellValue('A12', '1');
        $sheet->setCellValue('B12', '2');
        $sheet->setCellValue('C12', '3');
        $sheet->setCellValue('D12', '4');
        $sheet->setCellValue('E12', '5');
        $sheet->setCellValue('F12', '6');
        $sheet->setCellValue('G12', '7');
        $sheet->setCellValue('H12', '8');
        $sheet->setCellValue('I12', '9');
        $sheet->setCellValue('J12', '10');
        $sheet->setCellValue('K12', '11');
        $sheet->setCellValue('L12', '12');
        $sheet->setCellValue('M12', '13');
        $sheet->setCellValue('N12', '14');
        $sheet->setCellValue('O12', '15');

        $sheet->setCellValue('A13', '1');
        $sheet->setCellValue('A14', '2');
        $sheet->setCellValue('A16', '2,1');
        $sheet->setCellValue('A17', '2,1');
        $sheet->setCellValue('A18', '2,3');
        $sheet->setCellValue('A19', '2,4');

        $sheet->setCellValue('B13', 'Cán bộ quản lý (từ Kế toán trưởng/PT Kế toán trở lên)');
        $sheet->setCellValue('B14', 'Người lao động');
        $sheet->setCellValue('B15', 'Trong đó:');
        $sheet->setCellValue('B16', 'Khối gián tiếp (các Phòng, Chi nhánh)');
        $sheet->setCellValue('B17', 'Khối kho');
        $sheet->setCellValue('B18', 'Khối CHXD');
        $sheet->setCellValue('B19', 'LĐ hợp đồng');
        $sheet->setCellValue('B20', 'Tổng cộng');

        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');
        $sheet->mergeCells('A4:O6');

        $sheet->getStyle('A1:O100')->getFont()->setName('Times New Roman');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('9')->setRowHeight(50);
            },
        ];
    }
}
