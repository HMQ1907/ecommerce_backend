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

class ExpatExport implements ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles, WithTitle
{
    use Exportable;

    protected $data;

    protected $enum;

    protected $contractor;

    public function __construct($data, $enum)
    {
        $employeesStaff = $data->filter(function ($item) {
            return $item->employee->type == Employee::TYPE_EXPAT;
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
            'F' => 13,
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
            'V' => 40,
            'W' => 22,
            'X' => 8,
            'Y' => 5,
            'Z' => 5,
            'AA' => 5,
            'AB' => 5,
            'AC' => 5,
            'AD' => 5,
            'AF' => 12,
            'AG' => 5,
            'AH' => 10,
            'AI' => 5,
            'AJ' => 5,
            'AK' => 8,
            'AL' => 8,
            'AM' => 8,
            'AN' => 8,
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
        $sheet->getCell('G5')->setValue('LVB');
        $sheet->getCell('G6')->setValue('(USD)');

        $sheet->getCell('H4')->setValue('SALARY');
        $sheet->getCell('H5')->setValue(Carbon::parse($this->enum['date'])->format('d-M-y'));
        $sheet->getCell('H6')->setValue('(USD)');

        $sheet->getCell('I4')->setValue('SALARY');
        $sheet->getCell('I5')->setValue(Carbon::parse($this->enum['date'])->format('d-M-y'));
        $sheet->getCell('I6')->setValue('(USD)');

        $sheet->getCell('I4')->setValue('ADD. SALARY');
        $sheet->getCell('I6')->setValue('(USD)');

        $sheet->getCell('J4')->setValue('ADD. SALARY');
        $sheet->getCell('J5')->setValue(Carbon::parse($this->enum['date'])->subMonth()->format('M').' & '.Carbon::parse($this->enum['date'])->format('M'));
        $sheet->getCell('J6')->setValue('(USD)');

        $sheet->getCell('K4')->setValue(' Living Allw.');

        $sheet->getCell('L4')->setValue('New Position All. ');
        $sheet->getCell('L6')->setValue('(USD)');

        $sheet->getCell('M4')->setValue('OT');
        $sheet->getCell('M6')->setValue(Carbon::parse($this->enum['date'])->format('d-M-y'));

        $sheet->getCell('N4')->setValue('Gross Payment');
        $sheet->getCell('N5')->setValue(Carbon::parse($this->enum['date'])->format('d-M-y'));
        $sheet->getCell('N6')->setValue('(USD)');

        $sheet->getCell('O4')->setValue('Gross Payment');
        $sheet->getCell('O5')->setValue(Carbon::parse($this->enum['date'])->format('d-M-y'));
        $sheet->getCell('O6')->setValue('(KIP)');

        $sheet->getCell('P4')->setValue('Pers Income');
        $sheet->getCell('P5')->setValue('Tax');
        $sheet->getCell('P6')->setValue('(KIP)');

        $sheet->getCell('Q4')->setValue('Net Pay');
        $sheet->getCell('Q5')->setValue(Carbon::parse($this->enum['date'])->format('d-M-y'));
        $sheet->getCell('Q6')->setValue('(KIP)');

        $sheet->getCell('R1')->setValue('CONFIDENTIAL-PERSONNEL');
        $sheet->getStyle('R1')->getFont()->getColor()->setARGB('0000FF');

        $sheet->getCell('R4')->setValue('DATE OF');
        $sheet->getCell('R5')->setValue('BIRTH');
        $sheet->getCell('R6')->setValue('(D/M/Y)');

        $sheet->getCell('S4')->setValue('DATE TO');
        $sheet->getCell('S5')->setValue('COMPANY');
        $sheet->getCell('S6')->setValue('(D/M/Y)');

        $sheet->getCell('T4')->setValue('DATE TO');
        $sheet->getCell('T5')->setValue('JOB');
        $sheet->getCell('T6')->setValue('GROUP');

        $sheet->getCell('U5')->setValue('JOB');
        $sheet->getCell('V5')->setValue('EDUC');

        $sheet->getCell('W4')->setValue('ACTUA WORKING DAYS');
        $sheet->getCell('W5')->setValue('IN '.Carbon::parse($this->enum['date'])->subYear()->format('Y'));

        $sheet->getCell('X4')->setValue('GANDER');
        $sheet->getCell('X5')->setValue('M/F');

        $sheet->getCell('Y5')->setValue('Y');
        $sheet->getCell('Z4')->setValue('AGE');
        $sheet->getCell('Z5')->setValue('M');
        $sheet->getCell('AA5')->setValue('D');

        $sheet->getCell('AB5')->setValue('Y');
        $sheet->getCell('AC4')->setValue('SERVICE');
        $sheet->getCell('AC5')->setValue('M');
        $sheet->getCell('AD5')->setValue('D');

        $sheet->getCell('AF4')->setValue('Normal');
        $sheet->getCell('AF5')->setValue('Retirement');
        $sheet->getCell('AF6')->setValue('(D/M/Y)');

        $sheet->getCell('AG4')->setValue('AGE');

        $sheet->getCell('AH4')->setValue('SERVICE');
        $sheet->getCell('AH5')->setValue('(YY)');

        $sheet->getCell('AK4')->setValue('DATE OF');
        $sheet->getCell('AK5')->setValue('BIRTH');
        $sheet->getCell('W1')->setValue(Carbon::parse($this->enum['date'])->subMonth()->endOfMonth()->format('d-M-y'));
        $sheet->getStyle('W1')->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('W1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('00FF00');

        $sheet->getCell('AL4')->setValue('DATE OF');
        $sheet->getCell('AL5')->setValue('ENGAG.');

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
                            return $item->bank_name == 'LVB';
                        });
                        $sheet->getCell('G'. 7 + $no + $value)->setValue(count($accountBCEL) > 0 ? $accountBCEL->last()->account_number : '');
                    }
                    $sheet->getCell('H'. 7 + $no + $value)->setValue($payroll->gross_salary);
                    $sheet->getCell('H'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('I'. 7 + $no + $value)->setValue($payroll->employee->retaliations->count() > 0 ? $payroll->employee->retaliations->last()->original_amount_of_money : 0);
                    $sheet->getCell('I'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('J'. 7 + $no + $value)->setValue(0);
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
                    $sheet->getCell('K'. 7 + $no + $value)->setValue($allowanceMoney);
                    $sheet->getCell('K'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('L'. 7 + $no + $value)->setValue($newPositionAllowance);
                    $sheet->getCell('L'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('M'. 7 + $no + $value)->setValue($payroll->salary_json['amount_ot'] ?? 0);
                    $sheet->getCell('M'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('N'. 7 + $no + $value)->setValue(0);
                    $sheet->getCell('N'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('O'. 7 + $no + $value)->setValue($payroll->gross_salary ?? 0);
                    $sheet->getCell('O'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('P'. 7 + $no + $value)->setValue($payroll->salary_json['personal_income_tax'] ?? 0);
                    $sheet->getCell('P'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('Q'. 7 + $no + $value)->setValue($payroll->net_salary ?? 0);
                    $sheet->getCell('Q'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('R'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_of_birth)->format('d-M-y'));
                    $sheet->getCell('S'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_to_company)->format('d-M-y'));
                    $sheet->getCell('T'. 7 + $no + $value)->setValue(Carbon::parse($payroll->employee->date_to_job_group)->format('d-M-y'));
                    $sheet->getCell('U'. 7 + $no + $value)->setValue($payroll->employee->job ?? 0);
                    $sheet->getStyle('U'. 7 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('V'. 7 + $no + $value)->setValue($payroll->employee->education ?? '');
                    $sheet->getCell('W'. 7 + $no + $value)->setValue(365);
                    $sheet->getStyle('W'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('X'. 7 + $no + $value)->setValue($payroll->employee->gender == 'female' ? 'F' : 'M');
                    $sheet->getStyle('X'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    //set center
                    $birthDate = Carbon::parse($payroll->employee->date_of_birth);
                    $now = Carbon::now();
                    $ageYears = $now->diffInYears($birthDate);
                    $ageMonths = $now->copy()->subYears($ageYears)->diffInMonths($birthDate);
                    $ageDays = $now->copy()->subYears($ageYears)->subMonths($ageMonths)->diffInDays($birthDate);
                    $sheet->getCell('Y'. 7 + $no + $value)->setValue($ageYears);
                    $sheet->getStyle('Y'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('Z'. 7 + $no + $value)->setValue($ageMonths);
                    $sheet->getStyle('Z'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AA'. 7 + $no + $value)->setValue($ageDays);
                    $sheet->getStyle('AA'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $service = Carbon::parse($payroll->employee->date_to_company);
                    $now = Carbon::now();
                    $serviceYears = $now->diffInYears($service);
                    $serviceMonths = $now->copy()->subYears($serviceYears)->diffInMonths($service);
                    $serviceDays = $now->copy()->subYears($serviceYears)->subMonths($serviceMonths)->diffInDays($service);
                    $sheet->getCell('AB'. 7 + $no + $value)->setValue($serviceYears);
                    $sheet->getStyle('AB'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AC'. 7 + $no + $value)->setValue($serviceMonths);
                    $sheet->getStyle('AC'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AD'. 7 + $no + $value)->setValue($serviceDays);
                    $sheet->getStyle('AD'. 7 + $no + $value)->getAlignment()->setHorizontal('center');

                    $sheet->getCell('AF'. 7 + $no + $value)->setValue('=IF(ISBLANK(R'. 7 + $no + $value.'=""),"",EDATE(R'. 7 + $no + $value.',12*AG'. 7 + $no + $value.'))');
                    $sheet->getStyle('AF'. 7 + $no + $value)->getNumberFormat()->setFormatCode('d-mmm-yyyy');

                    $sheet->getCell('AG'. 7 + $no + $value)->setValue('=IF(X'. 7 + $no + $value.'="","",IF(X'. 7 + $no + $value.'="M","60",IF(X'. 7 + $no + $value.'="F","55","")))');
                    $sheet->getStyle('AG'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AH'. 7 + $no + $value)->setValue($payroll->employee->service ?? 0);
                    $sheet->getStyle('AH'. 7 + $no + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AK'. 7 + $no + $value)->setValue('=+$W$1-R'. 7 + $no + $value);
                    $sheet->getCell('AK'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AL'. 7 + $no + $value)->setValue(Carbon::parse($this->enum['date'])->subMonth()->diffInDays($payroll->employee->date_to_company));
                    $sheet->getCell('AL'. 7 + $no + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AM'. 7 + $no + $value)->setValue('=+TRUNC((AK'. 7 + $no + $value.'/365-TRUNC(AK'. 7 + $no + $value.'/365))*12)');
                    $sheet->getCell('AN'. 7 + $no + $value)->setValue('=+TRUNC((AL'. 7 + $no + $value.'/365-TRUNC(AL'. 7 + $no + $value.'/365))*12)');

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
                    $sheet->getCell('A'. 1 + $no + $flash + $value)->setValue($stt + $value + 1);
                    $sheet->getCell('B'. 1 + $no + $flash + $value)->setValue($payroll->employee->gender == 'female' ? 'Mrs' : 'Mr');
                    $sheet->getCell('C'. 1 + $no + $flash + $value)->setValue($payroll->employee->name);
                    $sheet->getCell('D'. 1 + $no + $flash + $value)->setValue($payroll->employee->designation->name ?? '');
                    $sheet->getCell('E'. 1 + $no + $flash + $value)->setValue($payroll->employee->indicator ?? '');
                    $sheet->getCell('F'. 1 + $no + $flash + $value)->setValue($payroll->employee->employee_code ?? '');
                    if ($payroll->employee->bankAccounts->count() > 0) {
                        $accountBCEL = $payroll->employee->bankAccounts->filter(function ($item) {
                            return $item->bank_name == 'LVB';
                        });
                        $sheet->getCell('G'. 1 + $no + $flash + $value)->setValue(count($accountBCEL) > 0 ? $accountBCEL->last()->account_number : '');
                    }
                    $sheet->getCell('H'. 1 + $no + $flash + $value)->setValue($payroll->gross_salary);
                    $sheet->getCell('H'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('I'. 1 + $no + $flash + $value)->setValue($payroll->employee->retaliations->count() > 0 ? $payroll->employee->retaliations->last()->original_amount_of_money : 0);
                    $sheet->getCell('I'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('J'. 1 + $no + $flash + $value)->setValue(0);
                    $sheet->getCell('J'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
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
                    $sheet->getCell('K'. 1 + $no + $flash + $value)->setValue($allowanceMoney);
                    $sheet->getCell('K'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('L'. 1 + $no + $flash + $value)->setValue($newPositionAllowance);
                    $sheet->getCell('L'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('M'. 1 + $no + $flash + $value)->setValue($payroll->salary_json['amount_ot'] ?? 0);
                    $sheet->getCell('M'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('N'. 1 + $no + $flash + $value)->setValue(0);
                    $sheet->getCell('N'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('O'. 1 + $no + $flash + $value)->setValue($payroll->gross_salary ?? 0);
                    $sheet->getCell('O'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('P'. 1 + $no + $flash + $value)->setValue($payroll->salary_json['personal_income_tax'] ?? 0);
                    $sheet->getCell('P'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('Q'. 1 + $no + $flash + $value)->setValue($payroll->net_salary ?? 0);
                    $sheet->getCell('Q'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('R'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_of_birth)->format('d-M-y'));
                    $sheet->getCell('S'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_to_company)->format('d-M-y'));
                    $sheet->getCell('T'. 1 + $no + $flash + $value)->setValue(Carbon::parse($payroll->employee->date_to_job_group)->format('d-M-y'));
                    $sheet->getCell('U'. 1 + $no + $flash + $value)->setValue($payroll->employee->job ?? 0);
                    $sheet->getStyle('U'. 7 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('V'. 1 + $no + $flash + $value)->setValue($payroll->employee->education ?? '');
                    $sheet->getCell('W'. 1 + $no + $flash + $value)->setValue(365);
                    $sheet->getStyle('W'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('X'. 1 + $no + $flash + $value)->setValue($payroll->employee->gender == 'female' ? 'F' : 'M');
                    $sheet->getStyle('X'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    //set center
                    $birthDate = Carbon::parse($payroll->employee->date_of_birth);
                    $now = Carbon::now();
                    $ageYears = $now->diffInYears($birthDate);
                    $ageMonths = $now->copy()->subYears($ageYears)->diffInMonths($birthDate);
                    $ageDays = $now->copy()->subYears($ageYears)->subMonths($ageMonths)->diffInDays($birthDate);
                    $sheet->getCell('Y'. 1 + $no + $flash + $value)->setValue($ageYears);
                    $sheet->getStyle('Y'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('Z'. 1 + $no + $flash + $value)->setValue($ageMonths);
                    $sheet->getStyle('Z'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AA'. 1 + $no + $flash + $value)->setValue($ageDays);
                    $sheet->getStyle('AA'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $service = Carbon::parse($payroll->employee->date_to_company);
                    $now = Carbon::now();
                    $serviceYears = $now->diffInYears($service);
                    $serviceMonths = $now->copy()->subYears($serviceYears)->diffInMonths($service);
                    $serviceDays = $now->copy()->subYears($serviceYears)->subMonths($serviceMonths)->diffInDays($service);
                    $sheet->getCell('AB'. 1 + $no + $flash + $value)->setValue($serviceYears);
                    $sheet->getStyle('AB'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AC'. 1 + $no + $flash + $value)->setValue($serviceMonths);
                    $sheet->getStyle('AC'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AD'. 1 + $no + $flash + $value)->setValue($serviceDays);
                    $sheet->getStyle('AD'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');

                    $sheet->getCell('AF'. 1 + $no + $flash + $value)->setValue('=IF(ISBLANK(R'. 1 + $no + $flash + $value.'=""),"",EDATE(R'. 1 + $no + $flash + $value.',12*AG'. 1 + $no + $flash + $value.'))');
                    $sheet->getStyle('AF'. 1 + $no + $flash + $value)->getNumberFormat()->setFormatCode('d-mmm-yyyy');

                    $sheet->getCell('AG'. 1 + $no + $flash + $value)->setValue('=IF(X'. 1 + $no + $flash + $value.'="","",IF(X'. 1 + $no + $flash + $value.'="M","60",IF(X'. 1 + $no + $flash + $value.'="F","55","")))');
                    $sheet->getStyle('AG'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AH'. 1 + $no + $flash + $value)->setValue($payroll->employee->service ?? 0);
                    $sheet->getStyle('AH'. 1 + $no + $flash + $value)->getAlignment()->setHorizontal('center');
                    $sheet->getCell('AK'. 1 + $no + $flash + $value)->setValue('=+$W$1-R'. 1 + $no + $flash + $value);
                    $sheet->getCell('AK'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AL'. 1 + $no + $flash + $value)->setValue(Carbon::parse($this->enum['date'])->subMonth()->diffInDays($payroll->employee->date_to_company));
                    $sheet->getCell('AL'. 1 + $no + $flash + $value)->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
                    $sheet->getCell('AM'. 1 + $no + $flash + $value)->setValue('=+TRUNC((AK'. 1 + $no + $flash + $value.'/365-TRUNC(AK'. 1 + $no + $flash + $value.'/365))*12)');
                    $sheet->getCell('AN'. 1 + $no + $flash + $value)->setValue('=+TRUNC((AL'. 1 + $no + $flash + $value.'/365-TRUNC(AL'. 1 + $no + $flash + $value.'/365))*12)');
                }
                $stt += count($item);
                $flash += count($item);
            }
        }
        $h = 0;
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;
        $m = 0;
        $n = 0;
        $o = 0;
        $p = 0;
        $q = 0;

        foreach ($inserRow as $key => $row) {
            $key++;
            $sheet->getCell('H'.+$row['start'])->setValue('=SUM(H'.$row['start'] + 1 .':H'.$row['end'].')');
            $h += $sheet->getCell('H'.+$row['start'])->getCalculatedValue();
            $sheet->getCell('H'.+$row['start'])->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

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

            // //Top and bottom boder in cell
            $sheet->getStyle('H'.+$row['start'].':Q'.+$row['start'])->getBorders()->getTop()->setBorderStyle('thin');
            $sheet->getStyle('H'.+$row['start'].':Q'.+$row['start'])->getBorders()->getBottom()->setBorderStyle('thin');
            $sheet->getStyle('H'.+$row['start'].':Q'.+$row['start'])->getFont()->getColor()->setARGB('0000FF');
            $sheet->getStyle('H'.+$row['start'].':Q'.+$row['start'])->getFont()->setItalic(true);
        }

        //GRAND TOTAL PERMANENT STAFF
        $sheet->getStyle('A'. 1 + $flash + count($this->data).':S'. 1 + $flash + count($this->data))->getFont()->getColor()->setARGB('0000FF');
        $sheet->getStyle('C'. 1 + $flash + count($this->data).':S'. 1 + $flash + count($this->data))->getFont()->setBold(true)->setItalic(true);
        $sheet->getCell('H'. 1 + $flash + count($this->data))->setValue($h);
        $sheet->getCell('H'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
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
        $sheet->getCell('V'. 1 + $flash + count($this->data))->getStyle()->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
        $sheet->getStyle('H'. 1 + $flash + count($this->data).':V'. 1 + $flash + count($this->data))->getFont()->setBold(true)->setItalic(true);
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
        return 'EXPAT';
    }
}
