<?php

namespace Modules\Employees\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\Branches\Models\Branch;

class TradingReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;

    protected $date;

    protected $branchId;

    public function __construct($data, array $params)
    {
        $this->data = $data;
        $this->date = Carbon::createFromFormat('Y-m', data_get($params, 'filter.month', now()))->format('M - Y');
        $this->branchId = auth()->user()->branch_id;
    }

    public function sheets(): array
    {
        $branchName = Branch::query()->find($this->branchId)->name;
        $sheets = [];
        $sheets[0] = new SalaryExport($this->data, $this->date, $branchName);
        $sheets[1] = new TaxExport($this->data, $this->date, $branchName);
        $sheets[2] = new OverTimeExport($this->data, $this->date);
        // $sheets[3] = new PaySlipExport($this->data, $this->date, $branchName);
        $sheets[3] = new BankAccountExport($this->data, $this->date, $branchName);
        $sheets[4] = new PayByCashExport($this->data, $this->date, $branchName);
        $sheets[5] = new PayByCash02Export($this->data, $this->date, $branchName);
        $sheets[6] = new VietNamStaffExport($this->data, $this->date, $branchName);
        $sheets[7] = new VietNamTaxExport($this->data, $this->date, $branchName);
        $sheets[8] = new VietNamBankAccountExport($this->data, $this->date, $branchName);
        // $sheets[9] = new NewPositionExport($this->data, $this->date, $branchName);

        return $sheets;
    }
}
