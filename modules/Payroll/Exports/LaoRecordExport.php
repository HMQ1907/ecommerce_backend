<?php

namespace Modules\Payroll\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\Branches\Models\Branch;
use Modules\Departments\Exports\GenderDepartmentExport;

class LaoRecordExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;

    protected $enum;

    protected $dataGender;

    protected $total;

    protected $dataRemoval;

    protected $newEmployee;

    public function __construct($data, $enum, $dataGender, $total, $dataRemoval, $newEmployee)
    {
        $this->data = $data;
        $this->enum = $enum;
        $this->dataGender = $dataGender;
        $this->total = $total;
        $this->dataRemoval = $dataRemoval;
        $this->newEmployee = $newEmployee;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[0] = new StaffExport($this->data, $this->enum);
        $sheets[1] = new ContractorExport($this->data, $this->enum);
        $sheets[2] = new ExpatExport($this->data, $this->enum);
        $branchName = Branch::query()->find($this->enum['branch_id'])->name;
        $sheets[3] = new GenderDepartmentExport($this->dataGender, $this->total, $branchName);
        $sheets[4] = new RemovalExport($this->dataRemoval, $this->enum);
        $sheets[5] = new NewEmployeeExport($this->newEmployee, $this->enum);

        return $sheets;
    }
}
