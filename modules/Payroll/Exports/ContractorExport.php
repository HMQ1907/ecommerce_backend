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

class ContractorExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $enum;

    protected $contractor;

    public function __construct($data, $enum)
    {
        $employeesStaff = $data->filter(function ($item) {
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
            'U' => 15,
            'V' => 16,
            'W' => 5,
            'X' => 12,
            'Y' => 12,
            'Z' => 12,
            'AA' => 12,
            'AB' => 12,
            'AC' => 22,
            'AD' => 30,
            'AE' => 13,
            'AF' => 13,
            'AG' => 5,
            'AH' => 5,
            'AI' => 5,
            'AJ' => 12,
            'AK' => 10,
            'AL' => 10,
            'AM' => 10,
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

        $sheet->getCell('K4')->setValue('Incent.Money');
        $sheet->getCell('K5')->setValue(Carbon::parse($this->enum['date'])->format('d-m-Y'));
        $sheet->getCell('K6')->setValue('(KIP)');

        $sheet->getCell('J4')->setValue('Add.Sal');
        $sheet->getCell('J6')->setValue('(KIP)');

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

        $sheet->getCell('W4')->setValue('JG');
        $sheet->getCell('W5')->setValue('');
        $sheet->getCell('W6')->setValue('');

        $sheet->getCell('X4')->setValue('BIRTHDAY');
        $sheet->getCell('X5')->setValue('MONTH');
        $sheet->getCell('X6')->setValue('(M)');

        $sheet->getCell('Y4')->setValue('BIRTHDAY');
        $sheet->getCell('Y5')->setValue('');
        $sheet->getCell('Y6')->setValue('(D/M/Y)');

        $sheet->getCell('Z4')->setValue('DAY OF');
        $sheet->getCell('Z5')->setValue('BIRTH');
        $sheet->getCell('Z6')->setValue('');

        $sheet->getCell('AA4')->setValue('DATE TO');
        $sheet->getCell('AA5')->setValue('JOINED');
        $sheet->getCell('AA6')->setValue('(D/M/Y)');

        $sheet->getCell('AB4')->setValue('DATE TO');
        $sheet->getCell('AB5')->setValue('JOB');
        $sheet->getCell('AB6')->setValue('GROUP');

        $sheet->getCell('AC4')->setValue('ACTUA WORKING DAYS');
        $sheet->getCell('AC5')->setValue('IN '.Carbon::parse($this->enum['date'])->subYear()->format('Y'));

        $sheet->getCell('AD4')->setValue('Educ');

        $sheet->getCell('AE5')->setValue('FROM');
        $sheet->getCell('AE4')->setValue('CURRENT CONTRACT');
        $sheet->getCell('AF5')->setValue('TO');
        $sheet->getCell('AG4')->setValue('GENDER');
        $sheet->getCell('AG5')->setValue('M/F');

        $sheet->getCell('AI4')->setValue('AGE');
        $sheet->getCell('AH5')->setValue('Y');
        $sheet->getCell('AI5')->setValue('M');
        $sheet->getCell('AJ5')->setValue('D');

        $sheet->getCell('AK4')->setValue('');
        $sheet->getCell('AK5')->setValue('Y');
        $sheet->getCell('AK6')->setValue('');

        $sheet->getCell('AL4')->setValue('SERVICE');
        $sheet->getCell('AL5')->setValue('M');
        $sheet->getCell('AL6')->setValue('');

        $sheet->getCell('AM4')->setValue('');
        $sheet->getCell('AM5')->setValue('D');
        $sheet->getCell('AM6')->setValue('');

        $sheet->getCell('AN5')->setValue('SERVICE');

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
                    // $sheet->getCell('J'. 7 + $no + $value)->setValue($payroll->salary_json['incent_money'] ?? '');
                    // $sheet->getCell('J'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('J'. 7 + $no + $value)->setValue($payroll->employee->retaliations->count() > 0 ? $payroll->employee->retaliations->last()->original_amount_of_money : 0);
                    $sheet->getCell('J'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $allowanceMoney = 0;
                    $newPositionAllowance = 0;
                    if (count($payroll->salary_json['main_allowances']) > 0) {
                        foreach ($payroll->salary_json['main_allowances'] as $key => $allowance) {
                            if ($allowance['component_id'] == 1) {
                                $allowanceMoney = $allowance['total'];
                            } elseif ($allowance['component_id'] == 2) {
                                $newPositionAllowance = $allowance['total'];
                            }
                        }
                    }
                    $sheet->getCell('K'. 7 + $no + $value)->setValue($payroll->salary_json['incent_money'] ?? '');
                    $sheet->getCell('K'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
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
                    $sheet->getCell('W'. 7 + $no + $value)->setValue($payroll->employee->jg);
                    $sheet->getCell('Z'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_of_birth)->format('d-M-y'));
                    $sheet->getStyle('Z'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AA'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_to_company)->format('d-M-y'));
                    $sheet->getCell('AB'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_to_job)->format('d-M-y'));

                    $sheet->getCell('AC'. 7 + $no + $value)->setValue(365);
                    $sheet->getCell('AC'. 7 + $no + $value)->getStyle()->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AD'. 7 + $no + $value)->setValue($payroll->employee->education ?? '');
                    $sheet->getCell('AE'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->contracts()->latest()->first()->contract_from)->format('d-M-y') ?? '');
                    $sheet->getCell('AF'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->contracts()->latest()->first()->contract_to)->format('d-M-y') ?? '');
                    $sheet->getCell('AG'. 7 + $no + $value)->setValue($payroll->employee->gender == 'female' ? 'F' : 'M');
                    $sheet->getCell('AG'. 7 + $no + $value)->getStyle()->getAlignment()->setHorizontal('center');

                    // set center
                    $birthDate = Carbon::parse($payroll->employee->date_of_birth);
                    $now = Carbon::now();
                    $ageYears = $now->diffInYears($birthDate);
                    $ageMonths = $now->copy()->subYears($ageYears)->diffInMonths($birthDate);
                    $ageDays = $now->copy()->subYears($ageYears)->subMonths($ageMonths)->diffInDays($birthDate);
                    $sheet->getCell('AH'. 7 + $no + $value)->setValue($ageYears);
                    $sheet->getStyle('AH'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AI'. 7 + $no + $value)->setValue($ageMonths);
                    $sheet->getStyle('AI'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AJ'. 7 + $no + $value)->setValue($ageDays);
                    $sheet->getStyle('AJ'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $service = Carbon::parse($payroll->employee->date_to_company);
                    $now = Carbon::now();
                    $serviceYears = $now->diffInYears($service);
                    $serviceMonths = $now->copy()->subYears($serviceYears)->diffInMonths($service);
                    $serviceDays = $now->copy()->subYears($serviceYears)->subMonths($serviceMonths)->diffInDays($service);
                    $sheet->getCell('AK'. 7 + $no + $value)->setValue($serviceYears);
                    $sheet->getStyle('AK'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AL'. 7 + $no + $value)->setValue($serviceMonths);
                    $sheet->getStyle('AL'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AM'. 7 + $no + $value)->setValue($serviceDays);
                    $sheet->getStyle('AM'. 7 + $no + $value)->getAlignment()->setHorizontal('center');

                    $sheet->getCell('AN'. 7 + $no + $value)->setValue($payroll->employee->service ?? 0);
                    $sheet->getStyle('AN'. 7 + $no + $value)->getAlignment()->setHorizontal('center');

                    $sheet->getCell('AO'. 7 + $no + $value)->setValue(Carbon::parse($this->enum['date'])->subMonth()->diffInDays($payroll->employee->date_of_birth));
                    $sheet->getCell('AO'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AP'. 7 + $no + $value)->setValue(Carbon::parse($this->enum['date'])->subMonth()->diffInDays($payroll->employee->date_to_company));
                    $sheet->getCell('AP'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AQ'. 7 + $no + $value)->setValue('=+TRUNC((AO'. 7 + $no + $value.'/365-TRUNC(AO'. 7 + $no + $value.'/365))*12)');
                    $sheet->getCell('AR'. 7 + $no + $value)->setValue('=+TRUNC((AP'. 7 + $no + $value.'/365-TRUNC(AP'. 7 + $no + $value.'/365))*12)');

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
                    $sheet->getCell('C'. 1 + $no + $flash + $value)->setValue($payroll->employee->last_name);
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
                    $sheet->getCell('W'. 1 + $no + $flash + $value)->setValue($payroll->employee->jg);
                    $sheet->getCell('Z'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_of_birth)->format('d-M-y'));
                    $sheet->getStyle('Z'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AA'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_to_company)->format('d-M-y'));
                    $sheet->getCell('AB'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_to_job)->format('d-M-y'));

                    $sheet->getCell('AC'. 1 + $no + $flash + $value)->setValue(365);
                    $sheet->getCell('AC'. 1 + $no + $flash + $value)->getStyle()->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AD'. 1 + $no + $flash + $value)->setValue($payroll->employee->education ?? '');
                    $sheet->getCell('AE'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->contracts()->latest()->first()->contract_from)->format('d-M-y') ?? '');
                    $sheet->getCell('AF'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->contracts()->latest()->first()->contract_to)->format('d-M-y') ?? '');
                    $sheet->getCell('AG'. 1 + $no + $flash + $value)->setValue($payroll->employee->gender == 'female' ? 'F' : 'M');
                    $sheet->getCell('AG'. 1 + $no + $flash + $value)->getStyle()->getAlignment()->setHorizontal('center');

                    // set center
                    $birthDate = Carbon::parse($payroll->employee->date_of_birth);
                    $now = Carbon::now();
                    $ageYears = $now->diffInYears($birthDate);
                    $ageMonths = $now->copy()->subYears($ageYears)->diffInMonths($birthDate);
                    $ageDays = $now->copy()->subYears($ageYears)->subMonths($ageMonths)->diffInDays($birthDate);
                    $sheet->getCell('AH'. 1 + $no + $flash + $value)->setValue($ageYears);
                    $sheet->getStyle('AH'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AI'. 1 + $no + $flash + $value)->setValue($ageMonths);
                    $sheet->getStyle('AI'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AJ'. 1 + $no + $flash + $value)->setValue($ageDays);
                    $sheet->getStyle('AJ'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $service = Carbon::parse($payroll->employee->date_to_company);
                    $now = Carbon::now();
                    $serviceYears = $now->diffInYears($service);
                    $serviceMonths = $now->copy()->subYears($serviceYears)->diffInMonths($service);
                    $serviceDays = $now->copy()->subYears($serviceYears)->subMonths($serviceMonths)->diffInDays($service);
                    $sheet->getCell('AK'. 1 + $no + $flash + $value)->setValue($serviceYears);
                    $sheet->getStyle('AK'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AL'. 1 + $no + $flash + $value)->setValue($serviceMonths);
                    $sheet->getStyle('AL'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AM'. 1 + $no + $flash + $value)->setValue($serviceDays);
                    $sheet->getStyle('AM'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');

                    $sheet->getCell('AN'. 1 + $no + $flash + $value)->setValue($payroll->employee->service ?? 0);
                    $sheet->getStyle('AN'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');

                    $sheet->getCell('AO'. 1 + $no + $flash + $value)->setValue(Carbon::parse($this->enum['date'])->subMonth()->diffInDays($payroll->employee->date_of_birth));
                    $sheet->getCell('AO'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AP'. 1 + $no + $flash + $value)->setValue(Carbon::parse($this->enum['date'])->subMonth()->diffInDays($payroll->employee->date_to_company));
                    $sheet->getCell('AP'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AQ'. 1 + $no + $flash + $value)->setValue('=+TRUNC((AO'. 1 + $no + $flash + $value.'/365-TRUNC(AO'. 1 + $no + $flash + $value.'/365))*12)');
                    $sheet->getCell('AR'. 1 + $no + $flash + $value)->setValue('=+TRUNC((AP'. 1 + $no + $flash + $value.'/365-TRUNC(AP'. 1 + $no + $flash + $value.'/365))*12)');
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
        $sheet->getCell('C'. 1 + $flash + count($this->data))->setValue('GRAND TOTAL DIRECT CONTRACTORS');
        $sheet->getStyle('A'. 1 + $flash + count($this->data).':S'. 1 + $flash + count($this->data))->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('C'. 1 + $flash + count($this->data).':S'. 1 + $flash + count($this->data))->getFont()->setBold(true)->setItalic(true);
        $sheet->getCell('I'. 1 + $flash + count($this->data))->setValue($i);
        $sheet->getCell('I'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('J'. 1 + $flash + count($this->data))->setValue($j);
        $sheet->getCell('J'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('K'. 1 + $flash + count($this->data))->setValue($k);
        $sheet->getCell('K'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('L'. 1 + $flash + count($this->data))->setValue($l);
        $sheet->getCell('L'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('M'. 1 + $flash + count($this->data))->setValue($m);
        $sheet->getCell('M'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('N'. 1 + $flash + count($this->data))->setValue($n);
        $sheet->getCell('N'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('O'. 1 + $flash + count($this->data))->setValue($o);
        $sheet->getCell('O'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('P'. 1 + $flash + count($this->data))->setValue($p);
        $sheet->getCell('P'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('Q'. 1 + $flash + count($this->data))->setValue($q);
        $sheet->getCell('Q'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('R'. 1 + $flash + count($this->data))->setValue($r);
        $sheet->getCell('R'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('S'. 1 + $flash + count($this->data))->setValue($s);
        $sheet->getCell('S'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('T'. 1 + $flash + count($this->data))->setValue($t);
        $sheet->getCell('T'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('U'. 1 + $flash + count($this->data))->setValue($u);
        $sheet->getCell('U'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getCell('V'. 1 + $flash + count($this->data))->setValue($v);
        $sheet->getCell('V'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getStyle('I'. 1 + $flash + count($this->data).':V'. 1 + $flash + count($this->data))->getFont()->setBold(true)->setItalic(true);
        $sheet->insertNewColumnBefore('E');
        $sheet->removeColumn('M');
        $sheet->removeColumn('N');
        $sheet->removeColumn('O');

        $sheet->removeColumn('W');
        $sheet->removeColumn('V');
        $sheet->removeColumn('X');
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
        return 'CONTRACTOR';
    }
}
