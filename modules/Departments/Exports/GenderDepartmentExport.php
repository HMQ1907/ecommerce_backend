<?php

namespace Modules\Departments\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenderDepartmentExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $total;

    protected $branchName;

    public function __construct($data, $total, $branchName)
    {
        $this->data = $data;
        $this->total = $total;
        $this->branchName = $branchName;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'N' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getCell('A1')->setValue($this->branchName);
        $sheet->getCell('A2')->setValue('MANPOWER NUMBER SUMMARY - '.now()->format('M - Y'));
        //set color text
        $sheet->getStyle('A1:A2')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getCell('A4')->setValue('Department');
        //set text right
        $sheet->getCell('A5')->setValue('Staff Categories');
        $no = 1;
        foreach ($this->data as $key => $item) {
            $sheet->mergeCells(chr(65 + $no).'4:'.chr(66 + $no).'4');
            $sheet->mergeCells(chr(65 + $no).'11:'.chr(66 + $no).'11');
            $sheet->getCell(chr(65 + $no).'11')->setValue('=SUM('.chr(65 + $no).'10:'.chr(66 + $no).'10)');
            $sheet->getCell(chr(65 + $no).'4')->setValue($item->department_name);
            $sheet->getCell(chr(65 + $no).'5')->setValue('Female');
            $sheet->getCell(chr(66 + $no).'5')->setValue('Male');
            $sheet->getCell(chr(65 + $no).'7')->setValue($item->female_expatriate_count);
            $sheet->getCell(chr(65 + $no + 1).'7')->setValue($item->male_expatriate_count);
            $sheet->getCell(chr(65 + $no).'8')->setValue($item->female_staff_count);
            $sheet->getCell(chr(65 + $no + 1).'8')->setValue($item->male_staff_count);
            $sheet->getCell(chr(65 + $no).'9')->setValue($item->female_contract_count);
            $sheet->getCell(chr(65 + $no + 1).'9')->setValue($item->male_contract_count);
            $sheet->getCell(chr(65 + $no).'10')->setValue('=SUM('.chr(65 + $no).'7:'.chr(65 + $no).'9)');
            $sheet->getCell(chr(65 + $no + 1).'10')->setValue('=SUM('.chr(65 + $no + 1).'7:'.chr(65 + $no + 1).'9)');

            $no += 2;
        }
        $sheet->getCell((chr(65 + count($this->data) * 2 + 1).'7'))->setValue($this->total['female_expatriate_count']);
        $sheet->getCell((chr(65 + count($this->data) * 2 + 2).'7'))->setValue($this->total['male_expatriate_count']);
        $sheet->getCell((chr(65 + count($this->data) * 2 + 1).'8'))->setValue($this->total['female_staff_count']);
        $sheet->getCell((chr(65 + count($this->data) * 2 + 2).'8'))->setValue($this->total['male_staff_count']);
        $sheet->getCell((chr(65 + count($this->data) * 2 + 1).'9'))->setValue($this->total['female_contract_count']);
        $sheet->getCell((chr(65 + count($this->data) * 2 + 2).'9'))->setValue($this->total['male_contract_count']);
        $sheet->getCell((chr(68 + count($this->data) * 2).'7'))->setValue('=SUM('.chr(66 + count($this->data) * 2).'7:'.chr(66 + count($this->data) * 2 + 1).'7)');
        $sheet->getCell((chr(68 + count($this->data) * 2).'8'))->setValue('=SUM('.chr(66 + count($this->data) * 2).'8:'.chr(66 + count($this->data) * 2 + 1).'8)');
        $sheet->getCell((chr(68 + count($this->data) * 2).'9'))->setValue('=SUM('.chr(66 + count($this->data) * 2).'9:'.chr(66 + count($this->data) * 2 + 1).'9)');
        $sheet->getCell((chr(68 + count($this->data) * 2).'10'))->setValue('=SUM('.chr(66 + count($this->data) * 2).'10:'.chr(66 + count($this->data) * 2 + 1).'10)');
        $sheet->getCell((chr(65 + count($this->data) * 2 + 2).'10'))->setValue('=SUM('.chr(66 + count($this->data) * 2 + 1).'7:'.chr(66 + count($this->data) * 2 + 1).'9)');
        $sheet->getCell((chr(65 + count($this->data) * 2 + 1).'10'))->setValue('=SUM('.chr(66 + count($this->data) * 2).'7:'.chr(66 + count($this->data) * 2).'9)');

        $sheet->mergeCells(chr(65 + count($this->data) * 2 + 1).'4:'.chr(65 + count($this->data) * 2 + 2).'4');
        $sheet->mergeCells(chr(65 + count($this->data) * 2 + 1).'11:'.chr(65 + count($this->data) * 2 + 2).'11');
        $sheet->getCell(chr(65 + count($this->data) * 2 + 1).'11')->setValue('=SUM('.chr(65 + $no).'10:'.chr(66 + $no).'10)');
        $sheet->getCell(chr(65 + count($this->data) * 2 + 1).'4')->setValue('Total');
        $sheet->getCell(chr(65 + count($this->data) * 2 + 1).'5')->setValue('Female');
        $sheet->getCell(chr(66 + count($this->data) * 2 + 1).'5')->setValue('Male');
        $sheet->getCell(chr(66 + count($this->data) * 2 + 2).'4')->setValue('Grand');
        $sheet->getCell(chr(66 + count($this->data) * 2 + 2).'5')->setValue('Total');

        $sheet->getStyle('A4:'.chr(66 + count($this->data) * 2).'11')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A4:'.chr(66 + count($this->data) * 2).'11')->getAlignment()->setVertical('center');
        $sheet->getStyle('A4:'.chr(66 + count($this->data) * 2).'5')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A4:'.chr(66 + count($this->data) * 2 + 2).'11')->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A4')->getAlignment()->setHorizontal('right');
        $sheet->getStyle('A5')->getAlignment()->setHorizontal('left');

        $sheet->getCell('A6')->setValue('CURRENT STAFF');
        $sheet->getStyle('A6')->getFont()->setSize(12)->setBold(true);
        $sheet->getCell('A7')->setValue('Expatriate Staff');
        $sheet->getCell('A8')->setValue('Local Staff');
        $sheet->getCell('A9')->setValue('Local Contractor');
        $sheet->getCell('A10')->setValue('Sub-Total');
        $sheet->getStyle('A10')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getCell('A11')->setValue('Total Actual no.');
        $sheet->getStyle('A11')->getFont()->getColor()->setARGB('800000');
        $sheet->getStyle('A1:'.chr(66 + count($this->data) * 2 + 2).'11')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FDE9D9');
        $sheet->getStyle('A1:O100')->getFont()->setName('Times New Roman');
        $sheet->setShowGridlines(false);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // $event->sheet->getDelegate()->getRowDimension('7')->setRowHeight(50);
            },
        ];
    }

    public function title(): string
    {
        return 'NUMBER';
    }
}
