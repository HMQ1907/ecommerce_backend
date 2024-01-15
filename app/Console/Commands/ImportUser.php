<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Excel;
use Modules\Users\Imports\ContractorImport;
use Modules\Users\Imports\ExpatImport;
use Modules\Users\Imports\RemovalImport;
use Modules\Users\Imports\StaffImport;

class ImportUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $branchId = 1; // TRADING
        (new StaffImport($branchId, [
            'title' => 1,
            'name' => 2,
            'department' => 3,
            'designation' => 4,
            'indicator' => 5,
            'employee_code' => 6,
            'bcel_bank' => 7,
            'lbd_bank' => 8,
            'salary' => 9,
            'housing' => 12,
            'position' => 13,
            'is_insurance' => 15,
            'date_of_birth' => 23,
            'date_to_company' => 27,
            'date_to_job_group' => 28,
            'job' => 29,
            'gender' => 30,
            'education' => 41,
        ]))->import(sprintf('%s-STAFF.xlsx', $branchId), 'public', Excel::XLSX);
        (new ContractorImport($branchId, [
            'title' => 1,
            'name' => 2,
            'department' => 3,
            'designation' => 4,
            'indicator' => 6,
            'employee_code' => 7,
            'bcel_bank' => 8,
            'lbd_bank' => 9,
            'salary' => 10,
            'housing' => 11,
            'position' => 12,
            'is_insurance' => 13,
            'date_of_birth' => 22,
            'date_to_company' => 23,
            'job' => 21,
            'gender' => 28,
            'education' => 25,
            'contract_from' => 26,
            'contract_to' => 27,
        ]))->import(sprintf('%s-CONTRACTOR.xlsx', $branchId), 'public', Excel::XLSX);
        (new ExpatImport($branchId, [
            'title' => 1,
            'name' => 2,
            'department' => 3,
            'designation' => 4,
            'indicator' => 5,
            'employee_code' => 6,
            'bcel_bank' => 7,
            'salary' => 8,
            'housing' => 11,
            'position' => 12,
            'date_of_birth' => 18,
            'date_to_company' => 19,
            'date_to_job_group' => 20,
            'job' => 21,
            'gender' => 24,
            'education' => 22,
        ]))->import(sprintf('%s-EXPAT.xlsx', $branchId), 'public', Excel::XLSX);
        (new RemovalImport($branchId, [
            'title' => 0,
            'name' => 1,
            'designation' => 2,
            'indicator' => 3,
            'employee_code' => 10,
            'salary' => 4,
            'housing' => 5,
            'position' => 6,
            'date_of_birth' => 7,
            'date_to_company' => 8,
            'job' => 9,
            'gender' => 11,
            'last_day' => 17,
            'remarks' => 18,
        ]))->import(sprintf('%s-REMOVAL.xlsx', $branchId), 'public', Excel::XLSX);

        $branchId = 2; // LAO
        (new StaffImport($branchId, [
            'title' => 1,
            'name' => 2,
            'department' => 3,
            'designation' => 4,
            'indicator' => 5,
            'employee_code' => 6,
            'bcel_bank' => 7,
            'lbd_bank' => 8,
            'salary' => 9,
            'housing' => 11,
            'position' => 12,
            'is_insurance' => 14,
            'date_of_birth' => 22,
            'date_to_company' => 26,
            'date_to_job_group' => 27,
            'job' => 28,
            'gender' => 29,
            'education' => 40,
        ]))->import(sprintf('%s-STAFF.xlsx', $branchId), 'public', Excel::XLSX);
        (new ContractorImport($branchId, [
            'title' => 1,
            'name' => 2,
            'department' => 3,
            'designation' => 4,
            'indicator' => 7,
            'employee_code' => 8,
            'bcel_bank' => 9,
            'lbd_bank' => 10,
            'salary' => 11,
            'housing' => null,
            'position' => 13,
            'is_insurance' => null,
            'date_of_birth' => 25,
            'date_to_company' => 26,
            'job' => 24,
            'gender' => 32,
            'education' => 30,
            'contract_from' => 28,
            'contract_to' => 29,
        ]))->import(sprintf('%s-CONTRACTOR.xlsx', $branchId), 'public', Excel::XLSX);
        (new ExpatImport($branchId, [
            'title' => 1,
            'name' => 2,
            'department' => 3,
            'designation' => 4,
            'indicator' => 5,
            'employee_code' => 6,
            'bcel_bank' => 7,
            'salary' => 10,
            'housing' => 13,
            'position' => 12,
            'date_of_birth' => 19,
            'date_to_company' => 20,
            'date_to_job_group' => 21,
            'job' => 22,
            'gender' => 24,
            'education' => 9999,
        ]))->import(sprintf('%s-EXPAT.xlsx', $branchId), 'public', Excel::XLSX);
        (new RemovalImport($branchId, [
            'title' => 1,
            'name' => 2,
            'designation' => 3,
            'indicator' => 4,
            'employee_code' => 11,
            'salary' => 5,
            'housing' => 6,
            'position' => 7,
            'date_of_birth' => 8,
            'date_to_company' => 9,
            'job' => 10,
            'gender' => 12,
            'last_day' => 18,
            'remarks' => 19,
        ]))->import(sprintf('%s-REMOVAL.xlsx', $branchId), 'public', Excel::XLSX);
    }
}
