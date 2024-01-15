<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay slip</title>
    <style>
        body {
            font-size: 16px;
        }

        .text {
            font-weight: 500;
        }

        tr:last-child {
            min-height: 500px;
        }
    </style>
</head>
<body>
<?php
$time = \Carbon\Carbon::parse(data_get($params, 'month'));
?>
<h2 align="center">{{ $time->isoFormat('MM-YYYY') }} {{ __('payroll::common.module') }}</h2>
<table border="0" style="border-collapse: collapse; width: 100%; font-weight: normal">
    <tr>
        <td>{{ !empty($employee->branch->name) ? $employee->branch->name : '-' }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid black">{{ __('payroll::common.payslip') }}</td>
        <td></td>
        <td></td>
        <td>{{ __('payroll::common.pay_period') }}</td>
        <td align="right">{{ \Carbon\Carbon::parse($data['salary_from'])->format('M-d') }}
            - {{ \Carbon\Carbon::parse($data['salary_to'])->format('M-d') }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ __('payroll::common.pay_day') }}</td>
        <td align="right">
            {{ !empty($data['paid_on']) ? \Carbon\Carbon::parse($data['paid_on'])->format('d-M-Y') : '-' }}
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold; color: red">{{ __('payroll::common.pay_day') }}:</td>
        <td style="font-weight: bold; color: red"
            colspan="2">{{ $employee->first_name }} {{ $employee->last_name }}</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>{{ __('payroll::common.cost_center') }}</td>
        <td>{{ !empty($employee->employee_code) ? $employee->employee_code : '-' }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="margin-top: 20px">
        <td align="center" colspan="2"
            style="border-bottom: 1px solid black; padding-top: 20px">{{ __('payroll::common.bank_account') }}:
        </td>
        <td align="center" colspan="3"
            style="border-bottom: 1px solid black; padding-top: 20px">{{ !empty($employee->bankAccounts[0]['account_number']) ? $employee->bankAccounts[0]['account_number'] : '-' }}</td>
    </tr>
    <tr style="border-top: 1px solid black">
        <td>{{ __('payroll::common.salary') }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right">{{ number_format($data['salary_json']['basic_salary']) }}</td>
    </tr>
    <tr>
        <td>{{ __('payroll::common.social_security') }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right">({{ number_format($data['salary_json']['social_security']) }})</td>
    </tr>
    <tr>
        <td>{{ __('payroll::common.position') }}</td>
        <td>{{ __('payroll::common.housing') }}</td>
        <td></td>
        <td></td>
        <td align="right">{{ number_format($data['salary_json']['fixed_allowance']) }}</td>
    </tr>
    <tr>
        <td>{{ __('payroll::common.overtime') }}</td>
        <td></td>
        <td align="right">Hrs</td>
        <td align="right">{{ number_format($data['salary_json']['hrs']) > 0 ? number_format($data['salary_json']['hrs']) : '-' }}</td>
        <td align="right">{{ number_format($data['salary_json']['amount_ot']) > 0 ? number_format($data['salary_json']['amount_ot']) : '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid black; padding-top: 20px">{{ __('payroll::common.total') }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right" style="border-bottom: 1px solid black">{{ number_format($data['gross_salary']) }}</td>
    </tr>
    <tr>
        <td>({{ __('payroll::common.reained_tax') }})</td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right">{{ number_format($data['salary_json']['personal_income_tax']) }}</td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid black; padding-top: 20px">{{ __('payroll::common.net_salary') }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right"
            style="border-bottom: 1px solid black; font-weight: bold; padding-top: 20px">{{ number_format($data['net_salary']) }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3" style="padding-top: 20px" align="right">{{ __('payroll::common.date') }}
            :____________________________
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3" align="right">{{ __('payroll::common.sign') }}:____________________________</td>
    </tr>
</table>
</body>
</html>
