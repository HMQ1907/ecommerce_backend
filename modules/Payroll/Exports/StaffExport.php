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

class StaffExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $enum;

    protected $contractor;

    public function __construct($data, $enum)
    {
        $employeesStaff = $data->filter(function ($item) {
            return $item->employee->type == Employee::TYPE_STAFF;
        });
        $this->contractor = $data->filter(function ($item) {
            return $item->employee->type == Employee::TYPE_CONTRACTOR;
        });
        $this->data = $employeesStaff->groupBy('employee.department.name');
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
        $sheet->getCell('C1')->setValue($branchName);
        $sheet->getStyle('C1')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('C1')->getFont()->setSize(11);
        $sheet->getStyle('C1')->getFont()->setBold(true);
        $sheet->getCell('C2')->setValue('PERMANENT STAFF RECORDS AS AT '.$this->enum['date']);
        //Format date in location VietNam
        //Header
        $sheet->getCell('C4')->setValue('NAME');
        $sheet->getCell('D4')->setValue('POSITION');
        $sheet->getStyle('C4:AL4')->getFont()->setBold(true)->setUnderline(true);
        $sheet->getCell('E4')->setValue('INDICATOR');

        $sheet->getCell('F4')->setValue('EMPLOYEE');
        $sheet->getCell('F5')->setValue('NUMBER');

        $sheet->getCell('G4')->setValue('BANK ACCOUNT');
        $sheet->getCell('G5')->setValue('BCEL');

        $sheet->getCell('H4')->setValue('BANK ACCOUNT');
        $sheet->getCell('H5')->setValue('LDB');
        $sheet->getCell('H6')->setValue('(KIP)');

        $sheet->getCell('I4')->setValue('SALARY');
        $sheet->getCell('I5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('I6')->setValue('(KIP)');

        $sheet->getCell('J4')->setValue('Incent.Money');
        $sheet->getCell('J5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('J6')->setValue('(KIP)');

        $sheet->getCell('K4')->setValue('Add.Sal');
        $sheet->getCell('K6')->setValue('(KIP)');

        $sheet->getCell('L4')->setValue('Allowance');
        $sheet->getCell('L5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('L6')->setValue('(KIP)');

        $sheet->getCell('M4')->setValue('New position');
        $sheet->getCell('M5')->setValue('Allowance');
        $sheet->getCell('M6')->setValue('(KIP)');

        $sheet->getCell('N4')->setValue('Total');
        $sheet->getCell('N5')->setValue('Allowance');
        $sheet->getCell('N6')->setValue('+Incentive');

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

        $sheet->getStyle('C4:AR6')->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle('C4:AR6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C4:AR6')->getAlignment()->setVertical('center');
        //Binding data
        $no = 0;
        $flash = $no + 6;
        $stt = 0;
        $inserRow = [];
        foreach ($this->data as $key => $item) {
            $no++;
            if ($no == 1) {
                $sheet->getCell('C'. 6 + $no)->setValue($key);
                $sheet->getStyle('C'. 6 + $no)->getFont()->setBold(true);
                $inserRow[] = [
                    'start' => 6 + $no,
                    'end' => count($item) + 6 + $no,
                ];
                foreach ($item as $value => $payroll) {
                    $sheet->getCell('A'. 7 + $no + $value)->setValue($stt + $value + 1);
                    $sheet->getCell('B'. 7 + $no + $value)->setValue($payroll->employee->gender == 'female' ? 'Mrs' : 'Mr');
                    $sheet->getCell('C'. 7 + $no + $value)->setValue($payroll->employee->name);
                    $sheet->getCell('D'. 7 + $no + $value)->setValue($payroll->employee->designation->name ?? '');
                    $sheet->getCell('E'. 7 + $no + $value)->setValue($payroll->employee->indicator ?? '');
                    $sheet->getCell('F'. 7 + $no + $value)->setValue($payroll->employee->employee_code ?? '');
                    if ($payroll->employee->bankAccounts->count() > 0) {
                        $accountBCEL = $payroll->employee->bankAccounts->filter(function ($item) {
                            return $item->bank_name == 'BCEL';
                        });
                        $sheet->getCell('G'. 7 + $no + $value)->setValue(count($accountBCEL) > 0 ? $accountBCEL->last()->account_number : '');

                        $accountLDB = $payroll->employee->bankAccounts->filter(function ($item) {
                            return $item->bank_name == 'LDB' ? $item : [];
                        });
                        $sheet->getCell('H'. 7 + $no + $value)->setValue(count($accountLDB) > 0 ? $accountLDB->last()->account_number : '');
                    }
                    $sheet->getCell('I'. 7 + $no + $value)->setValue($payroll->salary_json['basic_salary'] ?? 0);
                    $sheet->getCell('I'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('J'. 7 + $no + $value)->setValue($payroll->salary_json['incent_money'] ?? 0);
                    $sheet->getCell('J'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('K'. 7 + $no + $value)->setValue($payroll->employee->retaliations->count() > 0 ? $payroll->employee->retaliations->last()->original_amount_of_money : 0);
                    $sheet->getCell('K'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $allowanceMoney = '';
                    $newPositionAllowance = '';
                    if (count($payroll->salary_json['main_allowances']) > 0) {
                        foreach ($payroll->salary_json['main_allowances'] as $key => $allowance) {
                            if ($allowance['component_id'] == 1) {
                                $allowanceMoney = $allowance['total'];
                            } elseif ($allowance['component_id'] == 2) {
                                $newPositionAllowance = $allowance['total'];
                            }
                        }
                    }
                    $sheet->getCell('L'. 7 + $no + $value)->setValue($allowanceMoney);
                    $sheet->getCell('L'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('M'. 7 + $no + $value)->setValue($newPositionAllowance);
                    $sheet->getCell('M'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('N'. 7 + $no + $value)->setValue('=SUM(J'. 7 + $no + $value.':M'. 7 + $no + $value.')');
                    $sheet->getCell('N'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('O'. 7 + $no + $value)->setValue(empty($payroll->salary_json['social_security']) ? 'NO' : 'YES');
                    $sheet->getCell('O'. 7 + $no + $value)->getStyle()->getAlignment()->setHorizontal('center');
                    $sheet->getCell('P'. 7 + $no + $value)->setValue($payroll->salary_json['social_security'] ?? 0);
                    $sheet->getCell('P'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('Q'. 7 + $no + $value)->setValue($payroll->salary_json['amount_ot'] ?? 0);
                    $sheet->getCell('Q'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('R'. 7 + $no + $value)->setValue($payroll->gross_salary ?? 0);
                    $sheet->getCell('R'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('S'. 7 + $no + $value)->setValue($payroll->salary_json['personal_income_tax'] ?? 0);
                    $sheet->getCell('S'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('T'. 7 + $no + $value)->setValue($payroll->extra_json['total_deduction'] ?? 0);
                    $sheet->getCell('T'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('U'. 7 + $no + $value)->setValue($payroll->net_salary ?? 0);
                    $sheet->getCell('U'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('V'. 7 + $no + $value)->setValue($payroll->salary_json['insurance_salary'] ?? 0);
                    $sheet->getCell('V'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('W'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_of_birth)->format('d-M-y'));
                    $sheet->getCell('X'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_of_birth)->format('M'));
                    $sheet->getStyle('X'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AA'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_to_company)->format('d-M-y'));
                    $sheet->getCell('AB'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_to_job)->format('d-M-y'));

                    $sheet->getCell('AC'. 7 + $no + $value)->setValue($payroll->employee->job);
                    $sheet->getCell('AC'. 7 + $no + $value)->getStyle()->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AD'. 7 + $no + $value)->setValue($payroll->employee->gender == 'female' ? 'F' : 'M');
                    $sheet->getCell('AD'. 7 + $no + $value)->getStyle()->getAlignment()->setHorizontal('center');
                    //set center
                    $sheet->getStyle('AD'. 7 + $no + $value)->getAlignment()->setVertical('center');
                    $birthDate = Carbon::parse($payroll->employee->date_of_birth);
                    $now = Carbon::now();
                    $ageYears = $now->diffInYears($birthDate);
                    $ageMonths = $now->copy()->subYears($ageYears)->diffInMonths($birthDate);
                    $ageDays = $now->copy()->subYears($ageYears)->subMonths($ageMonths)->diffInDays($birthDate);
                    $sheet->getCell('AE'. 7 + $no + $value)->setValue($ageYears);
                    $sheet->getStyle('AE'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AF'. 7 + $no + $value)->setValue($ageMonths);
                    $sheet->getStyle('AF'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AG'. 7 + $no + $value)->setValue($ageDays);
                    $sheet->getStyle('AG'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $service = Carbon::parse($payroll->employee->date_to_company);
                    $now = Carbon::now();
                    $serviceYears = $now->diffInYears($service);
                    $serviceMonths = $now->copy()->subYears($serviceYears)->diffInMonths($service);
                    $serviceDays = $now->copy()->subYears($serviceYears)->subMonths($serviceMonths)->diffInDays($service);
                    $sheet->getCell('AH'. 7 + $no + $value)->setValue($serviceYears);
                    $sheet->getStyle('AH'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AI'. 7 + $no + $value)->setValue($serviceMonths);
                    $sheet->getStyle('AI'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AJ'. 7 + $no + $value)->setValue($serviceDays);
                    $sheet->getStyle('AJ'. 7 + $no + $value)->getAlignment()->setHorizontal('center');

                    $sheet->getCell('AK'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->normal_retirement_date)->format('d-M-Y'));
                    $sheet->getStyle('AK'. 7 + $no + $value)->getNumberFormat()->setFormatCode('d-mmm-yyyy');
                    $sheet->getCell('AL'. 7 + $no + $value)->setValue('=IF(AK'. 7 + $no + $value.'>=TODAY(),TEXT(AK'. 7 + $no + $value.'-TODAY(),"Y")&" Y",IF(AK'. 7 + $no + $value.'<TODAY(),"RETIREMENT",""))');
                    //if normal_retirement_date >= today return "RETIREMENT" else return number year & "Y"
                    $sheet->getCell('AL'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->normal_retirement_date)->gt(now()) ? Carbon::parse($payroll->employee->normal_retirement_date)->diffInYears(Carbon::now()).' Y' : 'RETIREMENT');
                    $sheet->getStyle('AL'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AM'. 7 + $no + $value)->setValue('=IF(AD'. 7 + $no + $value.'="","",IF(AD'. 7 + $no + $value.'="M","60",IF(AD'. 7 + $no + $value.'="F","55","")))');
                    $sheet->getStyle('AM'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AN'. 7 + $no + $value)->setValue($payroll->employee->service ?? 0);
                    $sheet->getStyle('AN'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AO'. 7 + $no + $value)->setValue($payroll->employee->education ?? '');
                    $sheet->getCell('AP'. 7 + $no + $value)->setValue('=IF(AN'. 7 + $no + $value.'>=1,"365",IF(AN'. 7 + $no + $value.'<1,($AQ$1-AA'. 7 + $no + $value.')))');
                    $sheet->getStyle('AP'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AQ'. 7 + $no + $value)->setValue('=+$AQ$1-W'. 7 + $no + $value);
                    $sheet->getCell('AQ'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    if ($payroll->employee->terminationAllowances()->count() > 0) {
                        $sheet->getCell('AR'. 7 + $no + $value)->setValue('=+$AK$'. 7 + $no + $value.'-AA'. 7 + $no + $value);
                    } else {
                        $sheet->getCell('AR'. 7 + $no + $value)->setValue('=+$AQ$1-AA'. 7 + $no + $value);
                    }
                    $sheet->getCell('AR'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AS'. 7 + $no + $value)->setValue('=+TRUNC((AQ'. 7 + $no + $value.'/365-TRUNC(AQ'. 7 + $no + $value.'/365))*12)');
                    $sheet->getCell('AT'. 7 + $no + $value)->setValue('=+TRUNC((AR'. 7 + $no + $value.'/365-TRUNC(AR'. 7 + $no + $value.'/365))*12)');

                    $flash++;
                }
            } else {
                $sheet->getCell('C'.+$no + $flash)->setValue($key);
                $sheet->getStyle('C'.$no + $flash)->getFont()->setBold(true);
                $inserRow[] = [
                    'start' => $no + $flash,
                    'end' => count($item) + $no + $flash,
                ];
                foreach ($item as $value => $payroll) {
                    $sheet->getCell('A'. 1 + $no + $flash + $value)->setValue($stt + $flash + $value + 1);
                    $sheet->getCell('B'. 1 + $no + $flash + $value)->setValue($payroll->employee->gender == 'female' ? 'Mrs' : 'Mr');
                    $sheet->getCell('C'. 1 + $no + $flash + $value)->setValue($payroll->employee->name);
                    $sheet->getCell('D'. 1 + $no + $flash + $value)->setValue($payroll->employee->designation->name ?? '');
                    $sheet->getCell('E'. 1 + $no + $flash + $value)->setValue($payroll->employee->indicator ?? '');
                    $sheet->getCell('F'. 1 + $no + $flash + $value)->setValue($payroll->employee->employee_code ?? '');
                    if ($payroll->employee->bankAccounts->count() > 0) {
                        $accountBCEL = $payroll->employee->bankAccounts->filter(function ($item) {
                            return $item->bank_name == 'BCEL';
                        });
                        $sheet->getCell('G'. 1 + $no + $flash + $value)->setValue(count($accountBCEL) > 0 ? $accountBCEL->last()->account_number : '');

                        $accountLDB = $payroll->employee->bankAccounts->filter(function ($item) {
                            return $item->bank_name == 'LDB' ? $item : [];
                        });
                        $sheet->getCell('H'. 1 + $no + $flash + $value)->setValue(count($accountLDB) > 0 ? $accountLDB->last()->account_number : '');
                    }
                    $sheet->getCell('I'. 1 + $no + $flash + $value)->setValue($payroll->salary_json['basic_salary'] ?? 0);
                    $sheet->getCell('I'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('J'. 1 + $no + $flash + $value)->setValue($payroll->salary_json['incent_money'] ?? '');
                    $sheet->getCell('J'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('K'. 1 + $no + $flash + $value)->setValue($payroll->employee->retaliations->count() > 0 ? $payroll->employee->retaliations->last()->original_amount_of_money : 0);
                    $sheet->getCell('K'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $allowanceMoney = '';
                    $newPositionAllowance = '';
                    if (count($payroll->salary_json['main_allowances']) > 0) {
                        foreach ($payroll->salary_json['main_allowances'] as $key => $allowance) {
                            if ($allowance['component_id'] == 1) {
                                $allowanceMoney = $allowance['total'];
                            } elseif ($allowance['component_id'] == 2) {
                                $newPositionAllowance = $allowance['total'];
                            }
                        }
                    }
                    $sheet->getCell('L'. 1 + $no + $flash + $value)->setValue($allowanceMoney);
                    $sheet->getCell('L'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('M'. 1 + $no + $flash + $value)->setValue($newPositionAllowance);
                    $sheet->getCell('M'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('N'. 1 + $no + $flash + $value)->setValue('=SUM(J'. 1 + $no + $flash + $value.':M'. 1 + $no + $flash + $value.')');
                    $sheet->getCell('N'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('O'. 1 + $no + $flash + $value)->setValue(empty($payroll->salary_json['social_security']) ? 'NO' : 'YES');
                    $sheet->getCell('O'. 1 + $no + $flash + $value)->getStyle()->getAlignment()->setHorizontal('center');
                    $sheet->getCell('P'. 1 + $no + $flash + $value)->setValue($payroll->salary_json['social_security'] ?? 0);
                    $sheet->getCell('P'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('Q'. 1 + $no + $flash + $value)->setValue($payroll->salary_json['amount_ot'] ?? 0);
                    $sheet->getCell('Q'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('R'. 1 + $no + $flash + $value)->setValue($payroll->gross_salary ?? 0);
                    $sheet->getCell('R'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('S'. 1 + $no + $flash + $value)->setValue($payroll->salary_json['personal_income_tax'] ?? 0);
                    $sheet->getCell('S'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('T'. 1 + $no + $flash + $value)->setValue($payroll->extra_json['total_deduction'] ?? 0);
                    $sheet->getCell('T'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('U'. 1 + $no + $flash + $value)->setValue($payroll->net_salary ?? 0);
                    $sheet->getCell('U'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('V'. 1 + $no + $flash + $value)->setValue($payroll->salary_json['insurance_salary'] ?? 0);
                    $sheet->getCell('V'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('W'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_of_birth)->format('d-M-y'));
                    $sheet->getCell('X'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_of_birth)->format('M'));
                    $sheet->getStyle('X'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AA'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_to_company)->format('d-M-y'));
                    $sheet->getCell('AB'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_to_job)->format('d-M-y'));

                    $sheet->getCell('AC'. 1 + $no + $flash + $value)->setValue($payroll->employee->job);
                    $sheet->getCell('AC'. 1 + $no + $flash + $value)->getStyle()->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AD'. 1 + $no + $flash + $value)->setValue($payroll->employee->gender == 'female' ? 'F' : 'M');
                    $sheet->getCell('AD'. 1 + $no + $flash + $value)->getStyle()->getAlignment()->setHorizontal('center');
                    //set center
                    $sheet->getStyle('AD'. 1 + $no + $flash + $value)->getAlignment()->setVertical('center');
                    $birthDate = Carbon::parse($payroll->employee->date_of_birth);
                    $now = Carbon::now();
                    $ageYears = $now->diffInYears($birthDate);
                    $ageMonths = $now->copy()->subYears($ageYears)->diffInMonths($birthDate);
                    $ageDays = $now->copy()->subYears($ageYears)->subMonths($ageMonths)->diffInDays($birthDate);
                    $sheet->getCell('AE'. 1 + $no + $flash + $value)->setValue($ageYears);
                    $sheet->getStyle('AE'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AF'. 1 + $no + $flash + $value)->setValue($ageMonths);
                    $sheet->getStyle('AF'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AG'. 1 + $no + $flash + $value)->setValue($ageDays);
                    $sheet->getStyle('AG'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $service = Carbon::parse($payroll->employee->date_to_company);
                    $now = Carbon::now();
                    $serviceYears = $now->diffInYears($service);
                    $serviceMonths = $now->copy()->subYears($serviceYears)->diffInMonths($service);
                    $serviceDays = $now->copy()->subYears($serviceYears)->subMonths($serviceMonths)->diffInDays($service);
                    $sheet->getCell('AH'. 1 + $no + $flash + $value)->setValue($serviceYears);
                    $sheet->getStyle('AH'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AI'. 1 + $no + $flash + $value)->setValue($serviceMonths);
                    $sheet->getStyle('AI'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AJ'. 1 + $no + $flash + $value)->setValue($serviceDays);
                    $sheet->getStyle('AJ'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');

                    $sheet->getCell('AK'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->normal_retirement_date)->format('d-M-Y'));
                    $sheet->getStyle('AK'. 1 + $no + $flash + $value)->getNumberFormat()->setFormatCode('d-mmm-yyyy');
                    $sheet->getCell('AL'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->normal_retirement_date)->gt(now()) ? Carbon::parse($payroll->employee->normal_retirement_date)->diffInYears(Carbon::now()).' Y' : 'RETIREMENT');
                    $sheet->getStyle('AL'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AM'. 1 + $no + $flash + $value)->setValue('=IF(AD'. 1 + $no + $flash + $value.'="","",IF(AD'. 1 + $no + $flash + $value.'="M","60",IF(AD'. 1 + $no + $flash + $value.'="F","55","")))');
                    $sheet->getStyle('AM'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AN'. 1 + $no + $flash + $value)->setValue($payroll->employee->service ?? 0);
                    $sheet->getStyle('AN'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AO'. 1 + $no + $flash + $value)->setValue($payroll->employee->education ?? '');
                    $sheet->getCell('AP'. 1 + $no + $flash + $value)->setValue('=IF(AN'. 1 + $no + $flash + $value.'>=1,"365",IF(AN'. 1 + $no + $flash + $value.'<1,($AQ$1-AA'. 1 + $no + $flash + $value.')))');
                    $sheet->getStyle('AP'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AQ'. 1 + $no + $flash + $value)->setValue('=+$AQ$1-W'. 1 + $no + $flash + $value);
                    $sheet->getCell('AQ'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    if ($payroll->employee->terminationAllowances()->count() > 0) {
                        $sheet->getCell('AR'. 1 + $no + $flash + $value)->setValue('=+$AK$'. 1 + $no + $flash + $value.'-AA'. 1 + $no + $flash + $value);
                    } else {
                        $sheet->getCell('AR'. 1 + $no + $flash + $value)->setValue('=+$AQ$1-AA'. 1 + $no + $flash + $value);
                    }
                    $sheet->getCell('AR'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AS'. 1 + $no + $flash + $value)->setValue('=+TRUNC((AQ'. 1 + $no + $flash + $value.'/365-TRUNC(AQ'. 1 + $no + $flash + $value.'/365))*12)');
                    $sheet->getCell('AT'. 1 + $no + $flash + $value)->setValue('=+TRUNC((AR'. 1 + $no + $flash + $value.'/365-TRUNC(AR'. 1 + $no + $flash + $value.'/365))*12)');
                }
                $stt += count($item);
                $flash += count($item);
            }
        }
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;
        $m = 0;
        $n = 0;
        $o = 0;
        $p = 0;
        $q = 0;
        $r = 0;
        $s = 0;
        $t = 0;
        $u = 0;
        $v = 0;

        foreach ($inserRow as $key => $row) {
            $key++;
            $sheet->getCell('I'.+$row['start'])->setValue('=SUM(I'.$row['start'] + 1 .':I'.$row['end'].')');
            $i += $sheet->getCell('I'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('I'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('J'.+$row['start'])->setValue('=SUM(J'.$row['start'] + 1 .':J'.$row['end'].')');
            $j += $sheet->getCell('J'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('J'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('K'.+$row['start'])->setValue('=SUM(K'.$row['start'] + 1 .':K'.$row['end'].')');
            $k += $sheet->getCell('K'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('K'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('L'.+$row['start'])->setValue('=SUM(L'.$row['start'] + 1 .':L'.$row['end'].')');
            $l += $sheet->getCell('L'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('L'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('M'.+$row['start'])->setValue('=SUM(M'.$row['start'] + 1 .':M'.$row['end'].')');
            $m += $sheet->getCell('M'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('M'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('N'.+$row['start'])->setValue('=SUM(N'.$row['start'] + 1 .':N'.$row['end'].')');
            $n += $sheet->getCell('N'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('N'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('O'.+$row['start'])->setValue('=SUM(O'.$row['start'] + 1 .':O'.$row['end'].')');
            $o += $sheet->getCell('O'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('O'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('P'.+$row['start'])->setValue('=SUM(P'.$row['start'] + 1 .':P'.$row['end'].')');
            $p += $sheet->getCell('P'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('P'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('Q'.+$row['start'])->setValue('=SUM(Q'.$row['start'] + 1 .':Q'.$row['end'].')');
            $q += $sheet->getCell('Q'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('Q'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('R'.+$row['start'])->setValue('=SUM(R'.$row['start'] + 1 .':R'.$row['end'].')');
            $r += $sheet->getCell('R'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('R'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('S'.+$row['start'])->setValue('=SUM(S'.$row['start'] + 1 .':S'.$row['end'].')');
            $s += $sheet->getCell('S'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('S'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('T'.+$row['start'])->setValue('=SUM(T'.$row['start'] + 1 .':T'.$row['end'].')');
            $t += $sheet->getCell('T'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('T'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('U'.+$row['start'])->setValue('=SUM(U'.$row['start'] + 1 .':U'.$row['end'].')');
            $u += $sheet->getCell('U'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('U'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            $sheet->getCell('V'.+$row['start'])->setValue('=SUM(V'.$row['start'] + 1 .':V'.$row['end'].')');
            $v += $sheet->getCell('V'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('V'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

            // //Top and bottom boder in cell
            $sheet->getStyle('I'.+$row['start'].':AP'.+$row['start'])->getBorders()->getTop()->setBorderStyle('thin');
            $sheet->getStyle('I'.+$row['start'].':AP'.+$row['start'])->getBorders()->getBottom()->setBorderStyle('thin');
            $sheet->getStyle('I'.+$row['start'].':AP'.+$row['start'])->getFont()->getColor()->setARGB('0000FF');
            $sheet->getStyle('I'.+$row['start'].':AP'.+$row['start'])->getFont()->setItalic(true);
        }

        //GRAND TOTAL PERMANENT STAFF
        $sheet->getCell('C'. 4 + $flash + count($this->data))->setValue('GRAND TOTAL PERMANENT STAFF');
        $sheet->getStyle('A'. 4 + $flash + count($this->data).':S'. 4 + $flash + count($this->data))->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('C'. 4 + $flash + count($this->data).':S'. 4 + $flash + count($this->data))->getFont()->setBold(true)->setItalic(true);
        $sheet->getCell('I'. 4 + $flash + count($this->data))->setValue($i);
        $sheet->getCell('I'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('J'. 4 + $flash + count($this->data))->setValue($j);
        $sheet->getCell('J'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('K'. 4 + $flash + count($this->data))->setValue($k);
        $sheet->getCell('K'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('L'. 4 + $flash + count($this->data))->setValue($l);
        $sheet->getCell('L'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('M'. 4 + $flash + count($this->data))->setValue($m);
        $sheet->getCell('M'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('N'. 4 + $flash + count($this->data))->setValue($n);
        $sheet->getCell('N'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('O'. 4 + $flash + count($this->data))->setValue($o);
        $sheet->getCell('O'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('P'. 4 + $flash + count($this->data))->setValue($p);
        $sheet->getCell('P'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('Q'. 4 + $flash + count($this->data))->setValue($q);
        $sheet->getCell('Q'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('R'. 4 + $flash + count($this->data))->setValue($r);
        $sheet->getCell('R'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('S'. 4 + $flash + count($this->data))->setValue($s);
        $sheet->getCell('S'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('T'. 4 + $flash + count($this->data))->setValue($t);
        $sheet->getCell('T'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('U'. 4 + $flash + count($this->data))->setValue($u);
        $sheet->getCell('U'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('V'. 4 + $flash + count($this->data))->setValue($v);
        $sheet->getCell('V'. 4 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getStyle('I'. 4 + $flash + count($this->data).':V'. 1 + $flash + count($this->data))->getFont()->setBold(true)->setItalic(true);
        //Total all record
        $sheet->getStyle('A'. 5 + $flash + count($this->data).':S'. 5 + $flash + count($this->data))->getFont()->getColor()->setARGB('FF0000');
        $sheet->getStyle('C'. 5 + $flash + count($this->data).':S'. 5 + $flash + count($this->data))->getFont()->setBold(true)->setItalic(true);
        //set border bottom
        $sheet->getStyle('I'. 5 + $flash + count($this->data).':V'. 5 + $flash + count($this->data))->getBorders()->getBottom()->setBorderStyle('double');
        $sheet->getCell('I'. 5 + $flash + count($this->data))->setValue('=I'. 4 + $flash + count($this->data).'+CONTRACTOR!J'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')));
        $sheet->getCell('I'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('J'. 5 + $flash + count($this->data))->setValue('=J'. 4 + $flash + count($this->data).'+CONTRACTOR!L'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')));
        $sheet->getCell('J'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('L'. 5 + $flash + count($this->data))->setValue('=L'. 4 + $flash + count($this->data));
        $sheet->getCell('L'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('M'. 5 + $flash + count($this->data))->setValue('=M'. 4 + $flash + count($this->data));
        $sheet->getCell('M'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('P'. 5 + $flash + count($this->data))->setValue('=P'. 4 + $flash + count($this->data).'+CONTRACTOR!N'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')));
        $sheet->getCell('P'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('R'. 5 + $flash + count($this->data))->setValue('=R'. 4 + $flash + count($this->data).'+CONTRACTOR!P'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')));
        $sheet->getCell('R'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('S'. 5 + $flash + count($this->data))->setValue('=S'. 4 + $flash + count($this->data).'+CONTRACTOR!Q'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')));
        $sheet->getCell('S'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('T'. 5 + $flash + count($this->data))->setValue('=T'. 4 + $flash + count($this->data).'+CONTRACTOR!R'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')));
        $sheet->getCell('T'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('U'. 5 + $flash + count($this->data))->setValue('=U'. 4 + $flash + count($this->data).'+CONTRACTOR!S'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')));
        $sheet->getCell('U'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('V'. 5 + $flash + count($this->data))->setValue('=V'. 4 + $flash + count($this->data).'+CONTRACTOR!V'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')).'+CONTRACTOR!T'.count($this->contractor) + 7 + count($this->contractor->groupBy('employee.department.name')));
        $sheet->getCell('V'. 5 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('D'. 6 + $flash + count($this->data))->setValue('Retirement fund 14.7%');
        $sheet->getCell('I'. 6 + $flash + count($this->data))->setValue('=I'. 5 + $flash + count($this->data).'*14.7%');
        $sheet->getCell('I'. 6 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getStyle('D'. 6 + $flash + count($this->data))->getFont()->setItalic(true);
        $sheet->getCell('A'. 7 + $flash + count($this->data))->setValue('Note :');
        $sheet->getStyle('A'. 7 + $flash + count($this->data))->getFont()->setUnderline(true)->setBold(true);
        $sheet->getCell('C'. 8 + $flash + count($this->data))->setValue('1. Staff will join Social Security Organisation Member only after completed probationary period.');
        $sheet->getCell('C'. 9 + $flash + count($this->data))->setValue('2. Emergency Loan of Mr. Phetsamone Phon Asa, Mechanic, (will be deducted 300,000 kip/month from 02/2022 to 11/2024)');
        $sheet->getStyle('C'. 8 + $flash + count($this->data))->getFont()->setBold(true);
        $sheet->getStyle('C'. 9 + $flash + count($this->data))->getFont()->getColor()->setARGB('FF0000');
        $noStt = 0;
        for ($i = 1; $i <= $flash + 6; $i++) {
            if ($sheet->getCell('B'.$i)->getValue() != null) {
                $noStt++;
                $sheet->getCell('A'.$i)->setValue($noStt);
            }
        }

        $sheet->getStyle('A1:T1000')->getFont()->setName('Times New Roman');
        $sheet->setShowGridlines(false);
    }

    public function title(): string
    {
        return 'STAFF';
    }
}
