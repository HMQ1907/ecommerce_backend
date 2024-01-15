<?php

namespace Modules\Payroll\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Branches\Models\Branch;
use Modules\Employees\Models\Employee;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RemovalExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $enum;

    protected $contractor;

    public function __construct($data, $enum)
    {
        $employeeRemove = $data->groupBy('employee.department.name')->flatten(1)->filter(function ($item) {
            return $item->employee->type == Employee::TYPE_REMOVAL;
        })->all();

        //Sắp xếp các lần thanh toán lương theo tháng từ thấp tới cao
        usort($employeeRemove, function ($item1, $item2) {
            return $item1['salary_to'] <=> $item2['salary_to'];
        });
        $result = [];
        foreach ($employeeRemove as $item) {
            $result[$item['employee_id']] = $item; // Ghi đè item cũ với item mới có cùng id
        }
        $this->data = $result;
        $this->enum = $enum;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 5,
            'C' => 30,
            'D' => 28,
            'E' => 15,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 16,
            'M' => 15,
            'N' => 15,
            'O' => 16,
            'P' => 15,
            'Q' => 15,
            'R' => 15,
            'S' => 15,
            'T' => 12,
            'U' => 12,
            'V' => 12,
            'W' => 12,
            'X' => 12,
            'Y' => 12,
            'Z' => 12,
            'AA' => 10,
            'AB' => 10,
            'AC' => 10,
            'AD' => 10,
            'AE' => 5,
            'AF' => 5,
            'AG' => 5,
            'AH' => 5,
            'AI' => 5,
            'AJ' => 5,
            'AK' => 15,
            'AL' => 15,
            'AM' => 15,
            'AN' => 15,
            'AO' => 40,
            'AP' => 18,
            'AQ' => 18,
            'AR' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                //
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $branchName = Branch::query()->find($this->enum['branch_id'])->name;
        $noStt = 0;
        $sheet->getCell('C1')->setValue($branchName);
        $sheet->getStyle('C1')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('C1')->getFont()->setSize(11);
        $sheet->getStyle('C1')->getFont()->setBold(true);
        $sheet->getCell('C2')->setValue('REMOVAL STAFF RECORDS DURING    :  1/6/2019 - Now');
        //Format date in location VietNam
        //Header
        $sheet->getCell('C4')->setValue('NAME');
        $sheet->getCell('D4')->setValue('POSITION');
        $sheet->getStyle('C4:AV4')->getFont()->setBold(true)->setUnderline(true);
        $sheet->getCell('E4')->setValue('INDICATOR');

        $sheet->getCell('F4')->setValue('EMPLOYEE');
        $sheet->getCell('F5')->setValue('NUMBER');

        $sheet->getCell('G4')->setValue('BANK ACCOUNT');
        $sheet->getCell('G5')->setValue('BCEL');

        $sheet->getCell('H4')->setValue('BANK ACCOUNT');
        $sheet->getCell('H5')->setValue('LDB');
        $sheet->getCell('H6')->setValue('(KIP)');

        $sheet->getCell('I4')->setValue('SALARY');
        $sheet->getCell('I6')->setValue('(KIP)');

        $sheet->getCell('J4')->setValue('Incent.Money');
        $sheet->getCell('J5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('J6')->setValue('(KIP)');

        $sheet->getCell('K4')->setValue('Add.Sal');
        $sheet->getCell('K6')->setValue('(KIP)');

        $sheet->getCell('L4')->setValue('Allowance');
        $sheet->getCell('L5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('L6')->setValue('(KIP)');

        $sheet->getCell('N4')->setValue('New position');
        $sheet->getCell('N5')->setValue('Allowance');
        $sheet->getCell('N6')->setValue('(KIP)');

        $sheet->getCell('M4')->setValue('Housing Allowance &');
        $sheet->getCell('M5')->setValue('Position Allowance');

        $sheet->getCell('O4')->setValue('SOCIAL');
        $sheet->getCell('O5')->setValue('WELFARE');
        $sheet->getCell('O6')->setValue('(KIP)');

        $sheet->getCell('P4')->setValue('LESS: 5.5%');
        $sheet->getCell('P5')->setValue('Social Sec.');
        $sheet->getCell('P6')->setValue('(KIP)');

        $sheet->getCell('Q4')->setValue('OT');
        $sheet->getCell('Q5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('Q6')->setValue('(KIP)');

        $sheet->getCell('R4')->setValue('Gross Payment');
        $sheet->getCell('R5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('R6')->setValue('(KIP)');

        $sheet->getCell('S4')->setValue('Pers Income');
        $sheet->getCell('S5')->setValue('Tax');
        $sheet->getCell('S6')->setValue('(KIP)');

        $sheet->getCell('T4')->setValue('Loan/Other');
        $sheet->getCell('T5')->setValue('Deduction');
        $sheet->getCell('T6')->setValue('(KIP)');

        $sheet->getCell('U4')->setValue('Net Pay');
        $sheet->getCell('U5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('U6')->setValue('(KIP)');

        $sheet->getCell('V1')->setValue('CONFIDENTIAL-PERSONNEL');
        $sheet->getStyle('V1')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getCell('V4')->setValue('Company 6.0%');
        $sheet->getCell('V5')->setValue('Social Sec.');
        $sheet->getCell('V6')->setValue('(KIP)');

        $sheet->getCell('W4')->setValue('DATE OF');
        $sheet->getCell('W5')->setValue('BIRTH');
        $sheet->getCell('W6')->setValue('(D/M/Y)');

        $sheet->getCell('X4')->setValue('BIRTHDAY');
        $sheet->getCell('X5')->setValue('MONTH');
        $sheet->getCell('X6')->setValue('(M)');

        $sheet->getCell('Y4')->setValue('BIRTHDAY');
        $sheet->getCell('Y5')->setValue('');
        $sheet->getCell('Y6')->setValue('STATUS');

        $sheet->getCell('Z4')->setValue('BIRTHDAY');
        $sheet->getCell('Z5')->setValue('Allowance');
        $sheet->getCell('Z6')->setValue('(KIP)');

        $sheet->getCell('AA4')->setValue('DATE TO');
        $sheet->getCell('AA5')->setValue('COMPANY');
        $sheet->getCell('AA6')->setValue('(D/M/Y)');

        $sheet->getCell('AB4')->setValue('DATE TO');
        $sheet->getCell('AB5')->setValue('JOB');
        $sheet->getCell('AB6')->setValue('GROUP');

        $sheet->getCell('AC5')->setValue('JOB');

        $sheet->getCell('AD4')->setValue('GANDER');
        $sheet->getCell('AD5')->setValue('M/F');

        $sheet->getCell('AE5')->setValue('Y');
        $sheet->getCell('AF4')->setValue('AGE');
        $sheet->getCell('AF5')->setValue('M');
        $sheet->getCell('AG5')->setValue('D');

        $sheet->getCell('AH5')->setValue('Y');
        $sheet->getCell('AI4')->setValue('SERVICE');
        $sheet->getCell('AI5')->setValue('M');
        $sheet->getCell('AJ5')->setValue('D');

        $sheet->getCell('AK4')->setValue('Normal');
        $sheet->getCell('AK5')->setValue('Retirement');
        $sheet->getCell('AK6')->setValue('(D/M/Y)');

        $sheet->getCell('AL4')->setValue('Retirement');
        $sheet->getCell('AL5')->setValue('Term');
        $sheet->getCell('AL6')->setValue('In (Y)');

        $sheet->getCell('AM4')->setValue('AGE');
        $sheet->getCell('AM5')->setValue('Retirement');

        $sheet->getCell('AN5')->setValue('SERVICE');

        $sheet->getCell('AO5')->setValue('Educ');

        $sheet->getCell('AP4')->setValue('ACTUA WORKING DAYS');
        $sheet->getCell('AP5')->setValue('IN '.Carbon::parse($this->enum['date'])->subYear()->format('Y'));

        $sheet->getCell('AQ4')->setValue('DATE OF');
        $sheet->getCell('AQ5')->setValue('BIRTH');
        $sheet->getCell('AQ1')->setValue(Carbon::parse($this->enum['date'])->endOfMonth()->format('d-M-y'));
        $sheet->getStyle('AQ1')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('AQ1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('00FF00');

        $sheet->getCell('AR4')->setValue('DATE OF');
        $sheet->getCell('AR5')->setValue('ENGAG.');
        $sheet->getCell('AU4')->setValue('LAST DAY');
        $sheet->getCell('AU5')->setValue('(YY)');
        $sheet->getCell('AV5')->setValue('REMARK');

        $sheet->getStyle('C4:AV6')->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle('C4:AV6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C4:AV6')->getAlignment()->setVertical('center');
        //Binding data
        $sttNo = 0;
        foreach ($this->data as $key => $item) {
            $sttNo++;
            $sheet->getCell('A'. 7 + $sttNo)->setValue($key + 1);
            $sheet->getCell('B'. 7 + $sttNo)->setValue($item->employee->gender == 'female' ? 'Mrs' : 'Mr');
            $sheet->getCell('C'. 7 + $sttNo)->setValue($item->employee->name);
            $sheet->getCell('D'. 7 + $sttNo)->setValue($item->employee->designation->name ?? '');
            $sheet->getCell('E'. 7 + $sttNo)->setValue($item->employee->indicator ?? '');
            $sheet->getCell('F'. 7 + $sttNo)->setValue($item->employee->employee_code ?? '');
            if ($item->employee->bankAccounts->count() > 0) {
                $accountBCEL = $item->employee->bankAccounts->filter(function ($item) {
                    return $item->bank_name == 'BCEL';
                });
                $sheet->getCell('G'. 7 + $sttNo)->setValue(count($accountBCEL) > 0 ? $accountBCEL->last()->account_number : '');

                $accountLDB = $item->employee->bankAccounts->filter(function ($item) {
                    return $item->bank_name == 'LDB' ? $item : [];
                });
                $sheet->getCell('H'. 7 + $sttNo)->setValue(count($accountLDB) > 0 ? $accountLDB->last()->account_number : '');
            }
            $sheet->getCell('I'. 7 + $sttNo)->setValue($item->gross_salary);
            $sheet->getCell('I'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('J'. 7 + $sttNo)->setValue('');
            $sheet->getCell('J'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $allowanceMoney = '';
            $newPositionAllowance = '';
            if (count($item->salary_json['main_allowances']) > 0) {
                foreach ($item->salary_json['main_allowances'] as $allowance) {
                    if ($allowance['component_id'] == 1) {
                        $allowanceMoney = $allowance['total'];
                    } elseif ($allowance['component_id'] == 2) {
                        $newPositionAllowance = $allowance['total'];
                    }
                }
            }
            $sheet->getCell('L'. 7 + $sttNo)->setValue($newPositionAllowance);
            $sheet->getCell('L'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('M'. 7 + $sttNo)->setValue($item->salary_json['fixed_allowance'] ?? 0);
            // $sheet->getCell('M'. 7+ $sttNo)->setValue($item->employee->transfers->count() > 0 ? $item->employee->transfers[count($item->employee->transfers) - 7]->new_position_allowance : '');
            $sheet->getCell('M'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('N'. 7 + $sttNo)->setValue($item->employee->transfers->count() > 0 ? $item->employee->transfers->last()->new_position_allowance : 0);
            $sheet->getCell('N'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('O'. 7 + $sttNo)->setValue(empty($item->salary_json['social_security']) ? 'NO' : 'YES');
            $sheet->getCell('O'. 7 + $sttNo)->getStyle()->getAlignment()->setHorizontal('center');
            $sheet->getCell('P'. 7 + $sttNo)->setValue($item->salary_json['social_security'] ?? 0);
            $sheet->getCell('P'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('Q'. 7 + $sttNo)->setValue($item->salary_json['amount_ot'] ?? 0);
            $sheet->getCell('Q'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('R'. 7 + $sttNo)->setValue($item->gross_salary ?? 0);
            $sheet->getCell('R'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('S'. 7 + $sttNo)->setValue($item->salary_json['personal_income_tax'] ?? 0);
            $sheet->getCell('S'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('T'. 7 + $sttNo)->setValue($item->extra_json['total_deduction'] ?? 0);
            $sheet->getCell('T'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('U'. 7 + $sttNo)->setValue($item->net_salary ?? 0);
            $sheet->getCell('U'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('V'. 7 + $sttNo)->setValue($item->salary_json['insurance_salary'] ?? 0);
            $sheet->getCell('V'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('W'. 7 + $sttNo)->setValue(Carbon::parse($item->employee->date_of_birth)->format('d-M-y'));
            $sheet->getCell('X'. 7 + $sttNo)->setValue(Carbon::parse($item->employee->date_of_birth)->format('M'));
            $sheet->getStyle('X'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AA'. 7 + $sttNo)->setValue(Carbon::parse($item->employee->date_to_company)->format('d-M-y'));
            $sheet->getCell('AB'. 7 + $sttNo)->setValue(Carbon::parse($item->employee->date_to_job)->format('d-M-y'));

            $sheet->getCell('AC'. 7 + $sttNo)->setValue($item->employee->job);
            $sheet->getCell('AC'. 7 + $sttNo)->getStyle()->getAlignment()->setHorizontal('center');
            $sheet->getCell('AD'. 7 + $sttNo)->setValue($item->employee->gender == 'female' ? 'F' : 'M');
            $sheet->getCell('AD'. 7 + $sttNo)->getStyle()->getAlignment()->setHorizontal('center');
            //set center
            $sheet->getStyle('AD'. 7 + $sttNo)->getAlignment()->setVertical('center');
            $birthDate = Carbon::parse($item->employee->date_of_birth);
            $now = Carbon::now();
            $ageYears = $now->diffInYears($birthDate);
            $ageMonths = $now->copy()->subYears($ageYears)->diffInMonths($birthDate);
            $ageDays = $now->copy()->subYears($ageYears)->subMonths($ageMonths)->diffInDays($birthDate);
            $sheet->getCell('AE'. 7 + $sttNo)->setValue($ageYears);
            $sheet->getStyle('AE'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AF'. 7 + $sttNo)->setValue($ageMonths);
            $sheet->getStyle('AF'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AG'. 7 + $sttNo)->setValue($ageDays);
            $sheet->getStyle('AG'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $service = Carbon::parse($item->employee->date_to_company);
            $now = Carbon::now();
            $serviceYears = $now->diffInYears($service);
            $serviceMonths = $now->copy()->subYears($serviceYears)->diffInMonths($service);
            $serviceDays = $now->copy()->subYears($serviceYears)->subMonths($serviceMonths)->diffInDays($service);
            $sheet->getCell('AH'. 7 + $sttNo)->setValue($serviceYears);
            $sheet->getStyle('AH'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AI'. 7 + $sttNo)->setValue($serviceMonths);
            $sheet->getStyle('AI'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AJ'. 7 + $sttNo)->setValue($serviceDays);
            $sheet->getStyle('AJ'. 7 + $sttNo)->getAlignment()->setHorizontal('center');

            $sheet->getCell('AK'. 7 + $sttNo)->setValue('=IF(ISBLANK(W'. 7 + $sttNo.'=""),"",EDATE(W'. 7 + $sttNo.',12*AM'. 7 + $sttNo.'))');
            $sheet->getStyle('AK'. 7 + $sttNo)->getNumberFormat()->setFormatCode('d-mmm-yyyy');
            $sheet->getCell('AL'. 7 + $sttNo)->setValue('=IF(AK'. 7 + $sttNo.'>=TODAY(),TEXT(AK'. 7 + $sttNo.'-TODAY(),"Y")&" Y",IF(AK'. 7 + $sttNo.'<TODAY(),"RETIREMENT",""))');
            $sheet->getStyle('AL'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AM'. 7 + $sttNo)->setValue('=IF(AD'. 7 + $sttNo.'="","",IF(AD'. 7 + $sttNo.'="M","60",IF(AD'. 7 + $sttNo.'="F","55","")))');
            $sheet->getStyle('AM'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AN'. 7 + $sttNo)->setValue($item->employee->service ?? 0);
            $sheet->getStyle('AN'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AO'. 7 + $sttNo)->setValue($item->employee->education ?? '');
            $sheet->getCell('AP'. 7 + $sttNo)->setValue(365);
            $sheet->getStyle('AP'. 7 + $sttNo)->getAlignment()->setHorizontal('center');
            $sheet->getCell('AQ'. 7 + $sttNo)->setValue('=+$AQ$1-W'. 7 + $sttNo);
            $sheet->getCell('AQ'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('AR'. 7 + $sttNo)->setValue(Carbon::parse($this->enum['date'])->subMonth()->diffInDays($item->employee->date_to_company));
            $sheet->getCell('AR'. 7 + $sttNo)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            $sheet->getCell('AS'. 7 + $sttNo)->setValue('=+TRUNC((AQ'. 7 + $sttNo.'/365-TRUNC(AQ'. 7 + $sttNo.'/365))*12)');
            $sheet->getCell('AT'. 7 + $sttNo)->setValue('=+TRUNC((AR'. 7 + $sttNo.'/365-TRUNC(AR'. 7 + $sttNo.'/365))*12)');
            $sheet->getCell('AU'. 7 + $sttNo)->setValue(!empty($item->employee->last_day) ? Carbon::parse($item->employee->last_day)->format('d-M-Y') : '');
            $sheet->getCell('AV'. 7 + $sttNo)->setValue($item->employee->remarks);
        }
        $sheet->removeColumn('G', 2);
        $sheet->removeColumn('H', 3);
        $sheet->removeColumn('J', 8);
        $sheet->removeColumn('K', 3);
        $sheet->removeColumn('L');
        $sheet->removeColumn('Q', 6);
        $sheet->removeColumn('R', 6);
        //set border bottom

        $sheet->getStyle('A1:T1000')->getFont()->setName('Times New Roman');
        $sheet->setShowGridlines(false);

    }

    public function title(): string
    {
        return 'REMOVAL';
    }
}
