<?php

namespace Modules\Employees\Exports;

use Laravel\Octane\Exceptions\DdException;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryReportExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles
{
    use Exportable;

    protected $data;

    protected $year;

    public function __construct($data, $year)
    {
        $this->data = $data;
        $this->year = $year;
    }

    public function columnWidths(): array
    {
        return [
            'B' => 40,
            'C' => 9,
            'Q' => 10,
            'R' => 10,
            'S' => 10,
            'T' => 10,
            'U' => 10,
            'V' => 10,
            'W' => 10,
        ];
    }

    /**
     * @throws Exception
     * @throws DdException
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:W1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:W2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:W4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A1:W1');
        $sheet->mergeCells('A2:W2');
        $sheet->mergeCells('A4:W4');
        $sheet->setCellValue('A1', 'TỔNG CÔNG TY DẦU VIỆT NAM - CTCP (PVOIL)');
        $sheet->setCellValue('A2', 'CÔNG TY:…………………………………');
        $sheet->setCellValue('A4', 'BÁO CÁO NHANH TÌNH HÌNH ƯỚC THỰC HIỆN LAO ĐỘNG VÀ THU NHẬP NĂM '.$this->year);
        $sheet->getStyle('A1:W4')->getFont()->setSize(13)->setBold(true);

        $sheet->getStyle('A6:W58')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A6:W8')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:W8')->getAlignment()->setVertical('center');
        $sheet->getStyle('A6:W8')->getFont()->setSize(12);
        $sheet->getStyle('A6:W8')->getFont()->setBold(true);
        $sheet->getStyle('A6:W8')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('A6:A8');
        $sheet->setCellValue('A6', 'TT');
        $sheet->mergeCells('B6:B8');
        $sheet->setCellValue('B6', 'Chi tiêu');
        $sheet->mergeCells('C6:C8');
        $sheet->setCellValue('C6', 'ĐVT');
        $sheet->mergeCells('D6:D7');
        $sheet->setCellValue('D6', 'NĂM TRƯỚC');
        $sheet->setCellValue('D8', 'Số liệu Q.toán');
        $sheet->mergeCells('E6:W6');
        $sheet->setCellValue('E6', 'NĂM NAY');
        $sheet->setCellValue('E7', 'T1');
        $sheet->setCellValue('E8', 'Số liệu thực hiện');
        $sheet->setCellValue('F7', 'T2');
        $sheet->setCellValue('F8', 'Số liệu thực hiện');
        $sheet->setCellValue('G7', 'T3');
        $sheet->setCellValue('G8', 'Số liệu thực hiện');
        $sheet->setCellValue('H7', 'T4');
        $sheet->setCellValue('H8', 'Số liệu thực hiện');
        $sheet->setCellValue('I7', 'T5');
        $sheet->setCellValue('I8', 'Số liệu thực hiện');
        $sheet->setCellValue('J7', 'T6');
        $sheet->setCellValue('J8', 'Số liệu thực hiện');
        $sheet->setCellValue('K7', 'T7');
        $sheet->setCellValue('K8', 'Số liệu thực hiện');
        $sheet->setCellValue('L7', 'T8');
        $sheet->setCellValue('L8', 'Số liệu thực hiện');
        $sheet->setCellValue('M7', 'T9');
        $sheet->setCellValue('M8', 'Số liệu thực hiện');
        $sheet->setCellValue('N7', 'T10');
        $sheet->setCellValue('N8', 'Số liệu thực hiện');
        $sheet->setCellValue('O7', 'T11');
        $sheet->setCellValue('O8', 'Số liệu thực hiện');
        $sheet->setCellValue('P7', 'T12');
        $sheet->setCellValue('P8', 'Số liệu thực hiện');
        $sheet->getStyle('E8:P8')->getFont()->setBold(false);
        $sheet->mergeCells('Q7:Q8');
        $sheet->setCellValue('Q7', 'Q1');
        $sheet->mergeCells('R7:R8');
        $sheet->setCellValue('R7', 'Q2');
        $sheet->mergeCells('S7:S8');
        $sheet->setCellValue('S7', 'Q3');
        $sheet->mergeCells('T7:T8');
        $sheet->setCellValue('T7', 'Q4');
        $sheet->mergeCells('U7:U8');
        $sheet->setCellValue('U7', '6 tháng đầu năm');
        $sheet->mergeCells('V7:V8');
        $sheet->setCellValue('V7', '9 tháng đầu năm');
        $sheet->mergeCells('W7:W8');
        $sheet->setCellValue('W7', 'Cả năm');
        $sheet->getStyle('Q7:Q58')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('CCC0DA');
        $sheet->getStyle('R7:R58')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('F2DCDB');
        $sheet->getStyle('S7:S58')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('DCE6F1');
        $sheet->getStyle('T7:T58')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('DCE6F1');
        $sheet->getStyle('U7:U58')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF99');
        $sheet->getStyle('V7:V58')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF99FF');
        $sheet->getStyle('W7:W58')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('B7DEE8');
        for ($i = 9; $i <= 58; $i++) {
            $sheet->setCellValue('A'.$i, $i - 8);
            if ($i == 9 || $i == 15 || $i == 16 || $i == 17 || $i == 23 || $i == 29 || $i == 35 || $i == 45) {
                $sheet->getStyle('A'.$i)->getFont()->setBold(true);
                $sheet->getStyle('B'.$i)->getFont()->setBold(true);
                $sheet->getStyle('A'.$i.':W'.$i)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF66');
            }
        }
        $sheet->getStyle('A9:W9')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FABF8F');
        $sheet->getStyle('A15:W15')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FABF8F');
        $sheet->getStyle('A17:W17')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('DAEEF3');
        $sheet->getStyle('A23:W23')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('DAEEF3');
        $sheet->setCellValue('B9', 'Lao động');
        $sheet->setCellValue('E9', 'QUÝ I');
        $sheet->setCellValue('F9', 'QUÝ I');
        $sheet->setCellValue('G9', 'QUÝ I');
        $sheet->setCellValue('H9', 'QUÝ II');
        $sheet->setCellValue('I9', 'QUÝ II');
        $sheet->setCellValue('J9', 'QUÝ II');
        $sheet->setCellValue('K9', 'QUÝ III');
        $sheet->setCellValue('L9', 'QUÝ III');
        $sheet->setCellValue('M9', 'QUÝ III');
        $sheet->setCellValue('N9', 'QUÝ IV');
        $sheet->setCellValue('O9', 'QUÝ IV');
        $sheet->setCellValue('P9', 'QUÝ IV');

        $sheet->freezePane('A10');
        $sheet->freezePane('E10');
        $spend = [
            'Số đầu kỳ',
            'Tăng trong kỳ',
            'Giảm trong kỳ',
            'Số cuối kỳ',
            'Lao động bình quân',
            'Các chỉ tiêu tiền lương và thu nhập thực chi',
            'Tiền lương (9+15) ',
            'Chi từ quỹ lương năm trước chuyển sang',
            'Lương hàng tháng',
            'Phụ cấp lương theo lương hàng tháng',
            'Các khoản bổ sung theo lương (PPL,...)',
            'Lương ca3',
            'Lương ngoài giờ',
            'Chi từ quỹ lương năm nay',
            'Lương hàng tháng',
            'Phụ cấp lương theo lương hàng tháng',
            'Các khoản bổ sung theo lương (PPL,...)',
            'Lương ca3',
            'Lương ngoài giờ',
            'BHXH chi trả thay lương',
            'Ốm đau',
            'Thai sản',
            'Tai nạn lao động',
            'Bệnh nghề nghiệp',
            'BHXH trả thay lương khác',
            'Thu nhập khác',
            'Quỹ khen thưởng',
            'Quỹ phúc lợi',
            'Quỹ khen thưởng BĐH',
            'Tiền ăn ca',
            'Phụ cấp đi biển',
            'Thưởng an toàn',
            'Thù lao',
            'Các khoản thu nhập khác chi bằng tiền',
            'Các khoản lợi ích khác',
            'Thu nhập chi trả cho CBQL kiêm nhiệm và các đối tượng khác',
            'Thù lao',
            'Quỹ khen thưởng',
            'Quỹ khen thưởng BĐH',
            'Quỹ phúc lợi',
            'Các khoản thu nhập khác chi bằng tiền',
            'Các khoản lợi ích khác',
            'Tổng quỹ thu nhập',
            'Tiền lương bình quân',
            'Thu nhập bình quân',
            'Sản lượng',
            'Doanh thu',
            'Lợi nhuận',
            'Tiền lương trích (Bao gồm TL ngoài giờ)',
        ];

        for ($i = 0; $i < count($spend); $i++) {
            $sheet->setCellValue('B'.(10 + $i), $spend[$i]);
        }
        $sheet->getStyle('C')->getFont()->setBold(true);
        $sheet->setCellValue('C10', 'Người');
        $sheet->setCellValue('C11', 'Người');
        $sheet->setCellValue('C12', 'Người');
        $sheet->setCellValue('C13', 'Người');
        $sheet->setCellValue('C14', 'Người');

        for ($i = 15; $i <= 58; $i++) {
            $sheet->setCellValue('C'.$i, 'Trđ');
        }
        //In 12 month value row 10
        for ($i = 0; $i < 12; $i++) {
            $sheet->setCellValue(chr(69 + $i).'10', '='.chr(69 + $i - 1).'10');
        }
        //Value in D10
        $sheet->setCellValue('D10', $this->data['last']['count_manager']);
        //Q1 -> all year row 10
        $sheet->setCellValue('Q10', '=E10');
        $sheet->getCell('Q10')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('R10', '=H10');
        $sheet->getCell('R10')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('S10', '=K10');
        $sheet->getCell('S10')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('T10', '=N10');
        $sheet->getCell('T10')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('U10', '=E10');
        $sheet->getCell('U10')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('V10', '=E10');
        $sheet->getCell('V10')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('W10', '=E10');
        $sheet->getCell('W10')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        //Q1 -> all year row 11
        $sheet->setCellValue('Q11', '=SUM(E11:G11)');
        $sheet->getCell('Q11')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('R11', '=SUM(H11:J11)');
        $sheet->getCell('R11')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('S11', '=SUM(K11:M11)');
        $sheet->getCell('S11')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('T11', '=SUM(N11:P11)');
        $sheet->getCell('T11')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('U11', '=SUM(E11:J11)');
        $sheet->getCell('U11')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('V11', '=SUM(E11:M11)');
        $sheet->getCell('V11')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('W11', '=SUM(E11:P11)');
        $sheet->getCell('W11')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        //Q1 -> all year row 12
        $sheet->setCellValue('Q12', '=SUM(E12:G12)');
        $sheet->getCell('Q12')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('R12', '=SUM(H12:J12)');
        $sheet->getCell('R12')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('S12', '=SUM(K12:M12)');
        $sheet->getCell('S12')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('T12', '=SUM(N12:P12)');
        $sheet->getCell('T12')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('U12', '=SUM(E12:J12)');
        $sheet->getCell('U12')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('V12', '=SUM(E12:M12)');
        $sheet->getCell('V12')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('W12', '=SUM(E12:P12)');
        $sheet->getCell('W12')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        //Value row 13
        for ($i = 0; $i < 20; $i++) {
            $sheet->setCellValue(chr(68 + $i). 13, '='.chr(68 + $i). 10 .'+'.chr(68 + $i). 11 .'-'.chr(68 + $i). 12);
            $sheet->getCell(chr(68 + $i). 13)->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        $sheet->setCellValue('D14', $this->data['avg_month']);
        $sheet->setCellValue('D11', $this->data['last_employee_up']);
        $sheet->setCellValue('D12', $this->data['last_employee_down']);
        foreach ($this->data['current_employee_up'] as $key => $value) {
            $sheet->setCellValue(chr(68 + $key + 1).'11', $value);
        }
        foreach ($this->data['current_employee_down'] as $key => $value) {
            $sheet->setCellValue(chr(68 + $key + 1).'12', $value);
        }
        foreach ($this->data['current']['avg_month'] as $key => $value) {
            $sheet->setCellValue(chr(68 + $key).'14', $value);
        }
        //Q1 -> All year row 14
        $sheet->setCellValue('Q14', '=SUM(E14:G14)/3');
        $sheet->getCell('Q14')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('R14', '=SUM(H14:J14)/3');
        $sheet->getCell('R14')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('S14', '=SUM(K14:M14)/3');
        $sheet->getCell('S14')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('T14', '=SUM(N14:P14)/3');
        $sheet->getCell('T14')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('U14', '=SUM(E14:J14)/6');
        $sheet->getCell('U14')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('V14', '=SUM(E14:M14)/9');
        $sheet->getCell('V14')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $sheet->setCellValue('W14', '=SUM(E14:P14)/12');
        $sheet->getCell('W14')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        //Value previous year row 16 column D
        $sheet->setCellValue('D16', '=D17+D23');
        for ($i = 0; $i < 19; $i++) {
            $sheet->setCellValue(chr(69 + $i).'16', '=SUM('.chr(69 + $i).'17:'.chr(69 + $i).'28)/2');
            $sheet->getCell(chr(69 + $i).'16')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value previous year row 17 column D
        for ($i = 0; $i < 20; $i++) {
            $sheet->setCellValue(chr(68 + $i).'17', '=SUM('.chr(68 + $i).'18:'.chr(68 + $i).'22)');
            $sheet->getCell(chr(68 + $i).'17')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 18 -> 22 column Q -> W
        for ($i = 18; $i <= 22; $i++) {
            $sheet->setCellValue('Q'.$i, '=SUM(E'.$i.':G'.$i.')');
            $sheet->getCell('Q'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('R'.$i, '=SUM(H'.$i.':J'.$i.')');
            $sheet->getCell('R'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('S'.$i, '=SUM(K'.$i.':M'.$i.')');
            $sheet->getCell('S'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('T'.$i, '=SUM(N'.$i.':P'.$i.')');
            $sheet->getCell('T'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('U'.$i, '=SUM(E'.$i.':J'.$i.')');
            $sheet->getCell('U'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('V'.$i, '=SUM(E'.$i.':M'.$i.')');
            $sheet->getCell('V'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('W'.$i, '=SUM(E'.$i.':P'.$i.')');
            $sheet->getCell('W'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value previous year row 23 column D
        for ($i = 0; $i < 20; $i++) {
            $sheet->setCellValue(chr(68 + $i).'23', '=SUM('.chr(68 + $i).'24:'.chr(68 + $i).'28)');
            $sheet->getCell(chr(68 + $i).'23')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 24 -> E -> P
        foreach ($this->data['current']['salary'] as $key => $value) {
            $sheet->setCellValue(chr(68 + $key).'24', $value);
            $sheet->getCell(chr(68 + $key).'24')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 26 E -> P
        $sheet->setCellValue('D26', $this->data['salary_supplements']);
        foreach ($this->data['current']['salary_supplements'] as $key => $value) {
            $sheet->setCellValue(chr(68 + $key).'26', $value);
            $sheet->getCell(chr(68 + $key).'26')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 24 -> row 28
        for ($i = 24; $i <= 28; $i++) {
            $sheet->setCellValue('Q'.$i, '=SUM(E'.$i.':G'.$i.')');
            $sheet->getCell('Q'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('R'.$i, '=SUM(H'.$i.':J'.$i.')');
            $sheet->getCell('R'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('S'.$i, '=SUM(K'.$i.':M'.$i.')');
            $sheet->getCell('S'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('T'.$i, '=SUM(N'.$i.':P'.$i.')');
            $sheet->getCell('T'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('U'.$i, '=SUM(E'.$i.':J'.$i.')');
            $sheet->getCell('U'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('V'.$i, '=SUM(E'.$i.':M'.$i.')');
            $sheet->getCell('V'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('W'.$i, '=SUM(E'.$i.':P'.$i.')');
            $sheet->getCell('W'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        foreach ($this->data['employee_ot'] as $key => $value) {
            $sheet->setCellValue(chr(68 + $key + 1).'28', $value);
            $sheet->getCell(chr(68 + $key).'28')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value previous year row 29 column D
        for ($i = 0; $i < 20; $i++) {
            $sheet->setCellValue(chr(68 + $i).'29', '=SUM('.chr(68 + $i).'30:'.chr(68 + $i).'34)');
            $sheet->getCell(chr(68 + $i).'29')->getStyle()->getNumberFormat()->setFormatCode('0.00');

        }
        //Value row 30 -> 34 column Q -> W
        for ($i = 30; $i <= 34; $i++) {
            $sheet->setCellValue('Q'.$i, '=SUM(E'.$i.':G'.$i.')');
            $sheet->getCell('Q'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('R'.$i, '=SUM(H'.$i.':J'.$i.')');
            $sheet->getCell('R'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('S'.$i, '=SUM(K'.$i.':M'.$i.')');
            $sheet->getCell('S'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('T'.$i, '=SUM(N'.$i.':P'.$i.')');
            $sheet->getCell('T'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('U'.$i, '=SUM(E'.$i.':J'.$i.')');
            $sheet->getCell('U'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('V'.$i, '=SUM(E'.$i.':M'.$i.')');
            $sheet->getCell('V'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('W'.$i, '=SUM(E'.$i.':P'.$i.')');
            $sheet->getCell('W'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value previous year row 35 column D
        for ($i = 0; $i < 20; $i++) {
            $sheet->setCellValue(chr(68 + $i).'35', '=SUM('.chr(68 + $i).'36:'.chr(68 + $i).'44)');
            $sheet->getCell(chr(68 + $i).'35')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 30 -> 34 column Q -> W
        for ($i = 36; $i <= 44; $i++) {
            $sheet->setCellValue('Q'.$i, '=SUM(E'.$i.':G'.$i.')');
            $sheet->getCell('Q'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('R'.$i, '=SUM(H'.$i.':J'.$i.')');
            $sheet->getCell('R'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('S'.$i, '=SUM(K'.$i.':M'.$i.')');
            $sheet->getCell('S'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('T'.$i, '=SUM(N'.$i.':P'.$i.')');
            $sheet->getCell('T'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('U'.$i, '=SUM(E'.$i.':J'.$i.')');
            $sheet->getCell('U'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('V'.$i, '=SUM(E'.$i.':M'.$i.')');
            $sheet->getCell('V'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('W'.$i, '=SUM(E'.$i.':P'.$i.')');
            $sheet->getCell('W'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 43 E -> P
        $sheet->setCellValue('D43', $this->data['salary_other_income']);
        foreach ($this->data['current']['salary_other_income'] as $key => $value) {
            $sheet->setCellValue(chr(68 + $key).'43', $value);
            $sheet->getCell(chr(68 + $key).'43')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value previous year row 45 column D
        for ($i = 0; $i < 20; $i++) {
            $sheet->setCellValue(chr(68 + $i).'45', '=SUM('.chr(68 + $i).'46:'.chr(68 + $i).'50)');
            $sheet->getCell(chr(68 + $i).'45')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 46 E -> P
        $sheet->setCellValue('D46', $this->data['salary_other_income']);
        foreach ($this->data['current']['salary_remuneration'] as $key => $value) {
            $sheet->setCellValue(chr(68 + $key).'46', $value);
            $sheet->getCell(chr(68 + $key).'46')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 46 -> 51 column Q -> W
        for ($i = 46; $i <= 51; $i++) {
            $sheet->setCellValue('Q'.$i, '=SUM(E'.$i.':G'.$i.')');
            $sheet->getCell('Q'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('R'.$i, '=SUM(H'.$i.':J'.$i.')');
            $sheet->getCell('R'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('S'.$i, '=SUM(K'.$i.':M'.$i.')');
            $sheet->getCell('S'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('T'.$i, '=SUM(N'.$i.':P'.$i.')');
            $sheet->getCell('T'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('U'.$i, '=SUM(E'.$i.':J'.$i.')');
            $sheet->getCell('U'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('V'.$i, '=SUM(E'.$i.':M'.$i.')');
            $sheet->getCell('V'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('W'.$i, '=SUM(E'.$i.':P'.$i.')');
            $sheet->getCell('W'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 52 column D -> W
        for ($i = 0; $i < 20; $i++) {
            $sheet->setCellValue(chr(68 + $i).'52', '='.chr(68 + $i).'16'.'+'.chr(68 + $i).'29'.'+'.chr(68 + $i).'35'.'+'.chr(68 + $i).'45');
            $sheet->getCell(chr(68 + $i).'52')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value row 53 column D -> W
        for ($i = 0; $i < 12; $i++) {
            $checkZero = $sheet->getCell(chr(68 + $i).'14')->getValue();
            if (chr(68 + $i).'53' == 'D53') {
                if ($checkZero == 0) {
                    $sheet->setCellValue(chr(68 + $i).'53', 0);

                    continue;
                }
                $sheet->setCellValue(chr(68 + $i).'53', '='.chr(68 + $i).'16'.'/'.chr(68 + $i).'14/12');
                $sheet->getCell(chr(68 + $i).'53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
            } else {
                if ($checkZero == 0) {
                    $sheet->setCellValue(chr(68 + $i).'53', 0);

                    continue;
                }
                $sheet->setCellValue(chr(68 + $i).'53', '='.chr(68 + $i).'16'.'/'.chr(68 + $i).'14');
                $sheet->getCell(chr(68 + $i).'53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
            }
        }
        $q14 = $sheet->getCell('Q14')->getCalculatedValue();
        $q16 = $sheet->getCell('Q16')->getCalculatedValue();
        $q14 == 0 || $q16 == 0 ? $sheet->setCellValue('Q53', 0) : $sheet->setCellValue('Q53', '=Q16/Q14/12');
        $sheet->getCell('Q53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $r16 = $sheet->getCell('R16')->getCalculatedValue();
        $r14 = $sheet->getCell('R14')->getCalculatedValue();
        $r14 == 0 || $r16 == 0 ? $sheet->setCellValue('R53', 0) : $sheet->setCellValue('R53', '=R16/R14/12');
        $sheet->getCell('R53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $s16 = $sheet->getCell('S16')->getCalculatedValue();
        $s14 = $sheet->getCell('S14')->getCalculatedValue();
        $s14 == 0 || $s16 == 0 ? $sheet->setCellValue('S53', 0) : $sheet->setCellValue('S53', '=S16/S14/12');
        $sheet->getCell('S53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $t16 = $sheet->getCell('T16')->getCalculatedValue();
        $t14 = $sheet->getCell('T14')->getCalculatedValue();
        $t14 == 0 || $t16 == 0 ? $sheet->setCellValue('T53', 0) : $sheet->setCellValue('T53', '=T16/T14/12');
        $sheet->getCell('T53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $u16 = $sheet->getCell('U16')->getCalculatedValue();
        $u14 = $sheet->getCell('U14')->getCalculatedValue();
        $u14 == 0 || $u16 == 0 ? $sheet->setCellValue('U53', 0) : $sheet->setCellValue('U53', '=U16/U14/12');
        $sheet->getCell('U53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $v16 = $sheet->getCell('V16')->getCalculatedValue();
        $v14 = $sheet->getCell('V14')->getCalculatedValue();
        $v14 == 0 || $v16 == 0 ? $sheet->setCellValue('V53', 0) : $sheet->setCellValue('V53', '=V16/V14/12');
        $sheet->getCell('V53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $w16 = $sheet->getCell('W16')->getCalculatedValue();
        $w14 = $sheet->getCell('W14')->getCalculatedValue();
        $w14 == 0 || $w16 == 0 ? $sheet->setCellValue('W53', 0) : $sheet->setCellValue('W53', '=W16/W14/12');
        $sheet->getCell('W53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        //Value P53 P54
        $p14 = $sheet->getCell('P14')->getCalculatedValue();
        $p14 == 0 ? $sheet->setCellValue('P53', 0) : $sheet->setCellValue('P53', '=P16/P14');
        $sheet->getCell('P53')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $p14 == 0 ? $sheet->setCellValue('P54', 0) : $sheet->setCellValue('P54', '==(P52-P45)/P14');
        $sheet->getCell('P54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        //Value row 54 column D -> W
        for ($i = 0; $i < 12; $i++) {
            $check = $sheet->getCell(chr(68 + $i).'14')->getCalculatedValue();
            if (chr(68 + $i).'54' == 'D54') {
                if ($check == 0) {
                    $sheet->setCellValue(chr(68 + $i).'54', 0);
                    $sheet->getCell(chr(68 + $i).'54')->getStyle()->getNumberFormat()->setFormatCode('0.00');

                    continue;
                }
                $sheet->setCellValue(chr(68 + $i).'54', '=('.chr(68 + $i).'52'.'-'.chr(68 + $i).'45)'.'/'.chr(68 + $i).'14'.'/'. 12);
                $sheet->getCell(chr(68 + $i).'54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
            } else {
                if ($check == 0) {
                    $sheet->setCellValue(chr(68 + $i).'54', 0);
                    $sheet->getCell(chr(68 + $i).'54')->getStyle()->getNumberFormat()->setFormatCode('0.00');

                    continue;
                }
                $sheet->setCellValue(chr(68 + $i).'54', '=('.chr(68 + $i).'52'.'-'.chr(68 + $i).'45)'.'/'.chr(68 + $i).'14');
                $sheet->getCell(chr(68 + $i).'54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
            }
        }
        $q14 == 0 ? $sheet->setCellValue('Q54', 0) : $sheet->setCellValue('Q54', '=(Q52-Q45)/Q14/3');
        $sheet->getCell('Q54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $r14 == 0 ? $sheet->setCellValue('R54', 0) : $sheet->setCellValue('R54', '=(R52-R45)/R14/3');
        $sheet->getCell('R54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $s14 == 0 ? $sheet->setCellValue('S54', 0) : $sheet->setCellValue('S54', '=(S52-S45)/S14/3');
        $sheet->getCell('S54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $t14 == 0 ? $sheet->setCellValue('T54', 0) : $sheet->setCellValue('T54', '=(T52-T45)/T14/3');
        $sheet->getCell('T54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $u14 == 0 ? $sheet->setCellValue('U54', 0) : $sheet->setCellValue('U54', '=(U52-U45)/U14/6');
        $sheet->getCell('U54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $v14 == 0 ? $sheet->setCellValue('V54', 0) : $sheet->setCellValue('V54', '=(V52-V45)/V14/9');
        $sheet->getCell('V54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        $w14 == 0 ? $sheet->setCellValue('W54', 0) : $sheet->setCellValue('W54', '=(W52-W45)/W14/12');
        $sheet->getCell('W54')->getStyle()->getNumberFormat()->setFormatCode('0.00');
        //Value row 55 -> 58 column Q -> W
        for ($i = 55; $i <= 58; $i++) {
            $sheet->setCellValue('Q'.$i, '=SUM(E'.$i.':G'.$i.')');
            $sheet->getCell('Q'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('R'.$i, '=SUM(H'.$i.':J'.$i.')');
            $sheet->getCell('R'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('S'.$i, '=SUM(K'.$i.':M'.$i.')');
            $sheet->getCell('S'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('T'.$i, '=SUM(N'.$i.':P'.$i.')');
            $sheet->getCell('T'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('U'.$i, '=SUM(E'.$i.':J'.$i.')');
            $sheet->getCell('U'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('V'.$i, '=SUM(E'.$i.':M'.$i.')');
            $sheet->getCell('V'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('W'.$i, '=SUM(E'.$i.':P'.$i.')');
            $sheet->getCell('W'.$i)->getStyle()->getNumberFormat()->setFormatCode('0.00');
        }
        //Value A59
        $sheet->mergeCells('A59:B59');
        $sheet->setCellValue('A59', 'DVN.TCT.KH.QĐ.04.BM.29');
        $sheet->getStyle('A59')->getFont()->setItalic(true)->setSize(10);
        //Value B60
        $sheet->setCellValue('B60', 'Quỹ lương còn lại chuyển sang năm sau');
        $sheet->setCellValue('B61', 'Quỹ lương chi từ nguồn năm trước');
        $sheet->setCellValue('B62', 'Chênh lệch');
        $sheet->getStyle('B60')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B61')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B62')->getFont()->setBold(true)->setSize(10);
        //Value C60
        $sheet->setCellValue('C60', 'Trđ');
        $sheet->setCellValue('C61', 'Trđ');
        $sheet->setCellValue('C62', 'Trđ');
        //Value D60
        $sheet->setCellValue('D60', '=D58-D23');
        $sheet->setCellValue('D61', '=W17');
        $sheet->setCellValue('D62', '=D60-D61');
        //Set color in footer table
        $sheet->getStyle('A52:W58')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FABF8F');
        $sheet->getStyle('A53:W54')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF66');
        $sheet->getStyle('B60:D60')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('00B0F0');
        $sheet->getStyle('B61:D61')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF00');
        $sheet->getStyle('B62:D62')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF0000');
        $sheet->getStyle('D10:W58')->getFont()->setBold(true);

        $sheet->getStyle('A1:O100')->getFont()->setName('Times New Roman');
    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('8')->setRowHeight(50);
            },
        ];
    }
}
