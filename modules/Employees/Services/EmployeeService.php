<?php

namespace Modules\Employees\Services;

use App\Services\BaseService;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Branches\Models\Branch;
use Modules\Employees\Events\EmployeeCreated;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeTerminationAllowance;
use Modules\Employees\Models\EmployeeTransfer;
use Modules\Employees\Notifications\ReminderNotification;
use Modules\Employees\Repositories\EmployeeBankAccountRepository;
use Modules\Employees\Repositories\EmployeeContractRepository;
use Modules\Employees\Repositories\EmployeeRepository;
use Modules\Overtimes\Models\Overtime;
use Modules\Payroll\Models\SalarySlip;
use Modules\Payroll\Services\PayrollService;
use Modules\Roles\Models\Role;
use Modules\Users\Services\UserService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeService extends BaseService
{
    protected $employeeRepository;

    protected $userService;

    protected $employeeBankAccountRepository;

    protected $employeeContractRepository;

    public function __construct(EmployeeRepository $employeeRepository, UserService $userService, EmployeeBankAccountRepository $employeeBankAccountRepository, EmployeeContractRepository $employeeContractRepository)
    {
        $this->employeeRepository = $employeeRepository;
        $this->userService = $userService;
        $this->employeeBankAccountRepository = $employeeBankAccountRepository;
        $this->employeeContractRepository = $employeeContractRepository;
    }

    public function getEmployees(array $params)
    {
        return QueryBuilder::for(Employee::allData())
            ->with([
                'user.setting',
                'user.roles',
                'branch',
                'department.manager',
                'designation',
                'bankAccounts',
                'contracts',
                'terminationAllowances',
            ])
            ->allowedIncludes(['user.roles', 'creator'])
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->searchName($q);
                }, null, ''),
                AllowedFilter::callback('roles', function (Builder $query, $roles) {
                    $query->whereHas('user.roles', function ($query) use ($roles) {
                        return $query->where('id', $roles);
                    });
                }),
                AllowedFilter::callback('role', function (Builder $query, $role) {
                    $query->byRole($role);
                }),
                AllowedFilter::callback('type', function (Builder $query, $type) {
                    $type = is_array($type) ? $type : [$type];
                    if (in_array(Employee::TYPE_REMOVAL, $type)) {
                        $query->withoutGlobalScope('active');
                    }
                    $query->whereIn('type', $type);
                }, null, ','),
                AllowedFilter::callback('start_date_between', function (Builder $query, $startDateBetween) {
                    $startDate = $startDateBetween[0];
                    $endDate = $startDateBetween[1];
                    $query->whereBetween('date_to_company', [$startDate, $endDate]);
                }),
                AllowedFilter::callback('department_id', function (Builder $query, $department) {
                    $department = is_array($department) ? $department : [$department];
                    $query->whereIn('department_id', $department);
                }),
                AllowedFilter::callback('birthday', function (Builder $query, $birthday) {
                    $query->whereMonth('date_of_birth', Carbon::parse($birthday)->month);
                }),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('employee_id', 'id'),
                AllowedFilter::exact('branch_id'),
                AllowedFilter::exact('designation_id'),
                AllowedFilter::exact('gender'),
            ])
            ->allowedSorts(['sort_order'])
            ->defaultSort('sort_order')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getEmployee($id)
    {
        return $this->employeeRepository->find($id);
    }

    public function createEmployee(array $attrs, $avatar = null)
    {
        try {
            DB::beginTransaction();

            // $user = $this->userService->createUser([
            //     'name' => data_get($attrs, 'first_name').' '.data_get($attrs, 'last_name'),
            //     'email' => data_get($attrs, 'user.email'),
            //     'password' => data_get($attrs, 'user.password'),
            // ]);

            // $user->assignRole(app(GeneralSettings::class)->default_role);
            // $user->syncPermissions($user->getAllPermissions()->pluck('id')->toArray());

            // if (empty($user)) {
            //     throw new \Exception();
            // }

            // $this->userService->syncUserRoles($user->id, ['roles' => data_get($attrs, 'user.roles')]);

            $values = [
                // 'user_id' => $user->id,
                'department_id' => data_get($attrs, 'department_id'),
                'designation_id' => data_get($attrs, 'designation_id'),
                'branch_id' => data_get($attrs, 'branch_id'),
                'first_name' => data_get($attrs, 'first_name'),
                'last_name' => data_get($attrs, 'last_name'),
                'email' => data_get($attrs, 'user.email'),
                'gender' => data_get($attrs, 'gender'),
                'date_of_birth' => data_get($attrs, 'date_of_birth'),
                'phone' => data_get($attrs, 'phone'),
                'address' => data_get($attrs, 'address'),
                'date_to_company' => data_get($attrs, 'date_to_company'),
                'status' => data_get($attrs, 'status'),
                'type' => data_get($attrs, 'type'),
                'position_type' => data_get($attrs, 'position_type'),
                'allowance' => data_get($attrs, 'allowance'),
                'indicator' => data_get($attrs, 'indicator'),
                'is_insurance' => data_get($attrs, 'is_insurance', true),
                'date_to_job' => data_get($attrs, 'date_to_job'),
                'job' => data_get($attrs, 'job'),
                'date_to_job_group' => data_get($attrs, 'date_to_job_group'),
                'date_of_engagement' => data_get($attrs, 'date_of_engagement'),
                'education' => data_get($attrs, 'education'),
                'jg' => data_get($attrs, 'jg'),
                'actua_working_days' => data_get($attrs, 'actua_working_days'),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            if ($avatar != null) {
                $values['avatar'] = $avatar->storeAs('avatar', $avatar->getClientOriginalName());
            }

            $employee = $this->employeeRepository->create($values);

            foreach (data_get($attrs, 'bank_accounts') as $bankAccount) {
                if (!empty(data_get($bankAccount, 'account_number'))) {
                    $this->employeeBankAccountRepository->create([
                        'employee_id' => $employee->id,
                        'account_holder_name' => data_get($bankAccount, 'account_holder_name'),
                        'account_number' => data_get($bankAccount, 'account_number'),
                        'bank_name' => data_get($bankAccount, 'bank_name'),
                        'bank_identifier_code' => data_get($bankAccount, 'bank_identifier_code'),
                        'branch_location' => data_get($bankAccount, 'branch_location'),
                        'tax_payer_id' => data_get($bankAccount, 'tax_payer_id'),
                    ]);
                }
            }

            foreach (data_get($attrs, 'contracts') as $contract) {
                if (!empty($contract)) {
                    if (!empty(data_get($contract, 'number'))) {
                        $employeeContract = $this->employeeContractRepository->create([
                            'employee_id' => $employee->id,
                            'type' => data_get($contract, 'type'),
                            'number' => data_get($contract, 'number'),
                            'contract_from' => data_get($contract, 'contract_from'),
                            'contract_to' => data_get($contract, 'contract_to'),
                            'created_at' => Carbon::now(),
                        ]);

                        $contractFileIds = data_get($contract, 'contract_file', '');
                        if ($contractFileIds) {
                            if (!is_array($contractFileIds)) {
                                $contractFileIds = explode(',', $contractFileIds);
                            }
                        } else {
                            $contractFileIds = [];
                        }
                        $employeeContract->syncMedia($contractFileIds, 'contract');
                    }
                }
            }

            // event(new EmployeeCreated($employee, data_get($attrs, 'password')));

            DB::commit();

            return $employee;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editEmployee($id, array $attrs, $avatar = null)
    {
        try {
            DB::beginTransaction();

            $values = [
                'designation_id' => data_get($attrs, 'designation_id'),
                'branch_id' => data_get($attrs, 'branch_id'),
                'first_name' => data_get($attrs, 'first_name'),
                'last_name' => data_get($attrs, 'last_name'),
                'gender' => data_get($attrs, 'gender'),
                'email' => data_get($attrs, 'user.email'),
                'date_of_birth' => data_get($attrs, 'date_of_birth'),
                'phone' => data_get($attrs, 'phone'),
                'address' => data_get($attrs, 'address'),
                'date_to_company' => data_get($attrs, 'date_to_company'),
                'status' => data_get($attrs, 'status'),
                'type' => data_get($attrs, 'type'),
                'position_type' => data_get($attrs, 'position_type'),
                'allowance' => data_get($attrs, 'allowance'),
                'indicator' => data_get($attrs, 'indicator'),
                'is_insurance' => data_get($attrs, 'is_insurance', true),
                'date_to_job' => data_get($attrs, 'date_to_job'),
                'job' => data_get($attrs, 'job'),
                'date_to_job_group' => data_get($attrs, 'date_to_job_group'),
                'date_of_engagement' => data_get($attrs, 'date_of_engagement'),
                'education' => data_get($attrs, 'education'),
                'jg' => data_get($attrs, 'jg'),
                'actua_working_days' => data_get($attrs, 'actua_working_days'),
                'updated_by' => auth()->user()->id,
            ];

            if ($this->isAdmin()) {
                $values['status'] = data_get($attrs, 'status');
                $values['type'] = data_get($attrs, 'type');
            }

            if ($avatar != null) {
                $values['avatar'] = $avatar->storeAs('avatar', $avatar->getClientOriginalName());
            }

            $employee = $this->employeeRepository->update($values, $id);

            DB::commit();

            return $employee;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteEmployee($id)
    {
        try {
            DB::beginTransaction();

            $this->employeeRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteEmployees(array $ids)
    {
        try {
            DB::beginTransaction();

            $employees = $this->employeeRepository->findWhereIn('id', $ids);
            foreach ($employees as $employee) {
                $employee->delete();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function sendMessage($id, array $attrs)
    {
        try {
            $employee = $this->employeeRepository->find($id);

            $employee->user->notify(new ReminderNotification(data_get($attrs, 'message'), auth()->user()));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getAttachments($id)
    {
        $employee = $this->employeeRepository->find($id);

        return $employee->getMedia('employee');
    }

    public function storeAttachment($employeeId, array $attrs)
    {
        try {
            DB::beginTransaction();

            $mediaId = data_get($attrs, 'media_id');
            $file = Media::find($mediaId);
            $file->variant_name = data_get($attrs, 'variant_name');
            $file->save();

            $employee = $this->employeeRepository->find($employeeId);
            $employee->attachMedia($mediaId, 'employee');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function updateAttachment($employeeId, $id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $employee = $this->employeeRepository->find($employeeId);
            $attachments = $employee->getMedia('employee');
            foreach ($attachments as $attachment) {
                if ($attachment->id == $id) {
                    $attachment->variant_name = data_get($attrs, 'variant_name');
                    $attachment->save();
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteAttachment($id)
    {
        try {
            DB::beginTransaction();

            Media::query()->find($id)->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getAttachment($id)
    {
        return Media::query()->find($id);
    }

    public function getEmployeeWorkingMonth($params)
    {
        $month = Carbon::parse($params['month']);
        $employeeId = data_get($params, 'filter.employee_id', null);

        $query = Employee::query()
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->whereIn('name', [Role::USER]);
                });
            })
            ->leftJoin('attendances', 'employees.id', '=', 'attendances.employee_id')
            ->whereMonth('attendances.date', $month)
            ->whereYear('attendances.date', Carbon::now()->year)
            ->groupBy('employees.id')
            ->select('employees.id', 'employees.last_name', 'employees.first_name')
            ->selectRaw('COUNT(DISTINCT DATE(attendances.date)) as workingDays');

        if ($employeeId) {
            $query->where('employees.id', $employeeId);
        }

        $employeeWorking = $query->get();

        $result = [];
        foreach ($employeeWorking as $employee) {
            $result[] = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->first_name.' '.$employee->last_name,
                'working_days' => $employee->workingDays,
            ];
        }

        return $result;
    }

    public function updatePersonal($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [
                'first_name' => data_get($attrs, 'first_name'),
                'last_name' => data_get($attrs, 'last_name'),
                'email' => data_get($attrs, 'email'),
                'gender' => data_get($attrs, 'gender'),
                'date_of_birth' => data_get($attrs, 'date_of_birth'),
                'phone' => data_get($attrs, 'phone'),
                'address' => data_get($attrs, 'address'),
                'education' => data_get($attrs, 'education'),
                'updated_by' => auth()->user()->id,
            ];

            $employee = $this->employeeRepository->update($values, $id);

            DB::commit();

            return $employee;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function updateBankAccount($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $employee = $this->employeeRepository->find($id);

            $employeeBankAccountsRequest = data_get($attrs, 'bank_accounts');
            $employeeBankAccountsRequest = array_filter($employeeBankAccountsRequest, function ($employeeBankAccountRequest) {
                return !empty(data_get($employeeBankAccountRequest, 'account_number'));
            });
            $arrayBankAccountIds = array_column($employeeBankAccountsRequest, 'id');
            $employeeBankAccounts = $employee->bankAccounts;
            foreach ($employeeBankAccounts as $employeeBankAccount) {
                if (!in_array($employeeBankAccount->id, $arrayBankAccountIds)) {
                    $employeeBankAccount->delete();
                }
            }
            foreach ($employeeBankAccountsRequest as $employeeBankAccountRequest) {
                $employeeBankAccount = $employeeBankAccounts->where('id', data_get($employeeBankAccountRequest, 'id'))->first();
                if ($employeeBankAccount) {
                    $employeeBankAccount->update([
                        'account_holder_name' => data_get($employeeBankAccountRequest, 'account_holder_name'),
                        'account_number' => data_get($employeeBankAccountRequest, 'account_number'),
                        'bank_name' => data_get($employeeBankAccountRequest, 'bank_name'),
                        'bank_identifier_code' => data_get($employeeBankAccountRequest, 'bank_identifier_code'),
                        'branch_location' => data_get($employeeBankAccountRequest, 'branch_location'),
                        'tax_payer_id' => data_get($employeeBankAccountRequest, 'tax_payer_id'),
                    ]);
                } else {
                    $this->employeeBankAccountRepository->create([
                        'employee_id' => $employee->id,
                        'account_holder_name' => data_get($employeeBankAccountRequest, 'account_holder_name') ?? '',
                        'account_number' => data_get($employeeBankAccountRequest, 'account_number'),
                        'bank_name' => data_get($employeeBankAccountRequest, 'bank_name'),
                        'bank_identifier_code' => data_get($employeeBankAccountRequest, 'bank_identifier_code'),
                        'branch_location' => data_get($employeeBankAccountRequest, 'branch_location'),
                        'tax_payer_id' => data_get($employeeBankAccountRequest, 'tax_payer_id'),
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }
            }

            $employee->save();

            DB::commit();

            return $employee;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function updateCompany($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $employee = $this->employeeRepository->find($id);

            if (isset($attrs['user']['roles'])) {
                $this->userService->syncUserRoles($employee->user_id, ['roles' => $attrs['user']['roles']]);
            }

            $employee = $this->employeeRepository->update([
                'branch_id' => data_get($attrs, 'branch_id'),
                'date_to_company' => data_get($attrs, 'date_to_company'),
                'type' => data_get($attrs, 'type'),
                'position_type' => data_get($attrs, 'position_type'),
                'indicator' => data_get($attrs, 'indicator'),
                'is_insurance' => data_get($attrs, 'is_insurance', true),
                'date_to_job' => data_get($attrs, 'date_to_job'),
                'job' => data_get($attrs, 'job'),
                'date_to_job_group' => data_get($attrs, 'date_to_job_group'),
                'jg' => data_get($attrs, 'jg'),
            ], $id);

            DB::commit();

            return $employee;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function resultByYear($branchId, $year, $positionType)
    {
        $result = [];
        $employees = Employee::query()
            ->with([
                'salarySlips',
                'department',
                'branch',
                'terminationAllowances',
                'transfers',
            ])
            ->where('employees.position_type', '=', $positionType)
            ->where('employees.branch_id', auth()->user()->branch_id)
            ->get();
        $result['count_manager'] = $employees->where('date_to_company', '<=', Carbon::createFromFormat('Y', $year)->addMonth()->endOfYear()->format('Y-m-d'))
            ->count();
        //array start end date in month by $year
        $arrayMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthDate = Carbon::createFromFormat('Y-m-d', $year.'-'.$i.'-1');
            $arrayMonth[$i] = [
                'start' => $monthDate->startOfMonth()->format('Y-m-d').' 00:00:00',
                'end' => $monthDate->endOfMonth()->format('Y-m-d').' 00:00:00',
            ];
        }
        $arrDayInYear = [];
        $dateTermination = 0;
        $salarySlip = SalarySlip::query()->whereIn('employee_id', $employees->pluck('id')->toArray())
            ->whereYear('salary_from', '=', $year)
            ->get();
        $allowance = EmployeeTerminationAllowance::query()
            ->whereIn('employee_id', $employees->pluck('id')->toArray())
            ->get();
        $transferFrom = EmployeeTransfer::query()
            ->whereIn('employee_id', $employees->pluck('id')->toArray())
            ->whereHas('fromDesignation', function ($query) use ($positionType) {
                $query->where('type', '=', $positionType);
            })
            ->get();
        $transferTo = EmployeeTransfer::query()
            ->whereIn('employee_id', $employees->pluck('id')->toArray())
            ->whereHas('toDesignation', function ($query) use ($positionType) {
                $query->where('type', '=', $positionType);
            })
            ->get();
        foreach ($arrayMonth as $keyMonth => $month) {
            $dayInMonth = CarbonPeriod::create($month['start'], $month['end']);
            //In table employee check date_to_job
            foreach ($dayInMonth as $date) {
                $countEmployeeInDate = $employees->where('date_to_company', '>=', $date)
                    ->where('date_to_company', '<=', Carbon::parse($date)->endOfMonth()->format('Y-m-d'))
                    ->count();
                $arrDayInYear[$keyMonth][$date->format('Y-m-d')] = $countEmployeeInDate;
                $dateRemoval = $allowance->where('termination_date', '=', $date)->all();
                $dateTermination -= collect($dateRemoval)->count();
                $dateTransferFrom = $transferFrom->where('transfer_date', '=', $date)
                    ->all();
                $dateTermination -= collect($dateTransferFrom)->count();
                $dateTransferTo = $transferTo->where('transfer_date', '=', $date)
                    ->all();
                $dateTermination += collect($dateTransferTo)->count();
                $arrDayInYear[$keyMonth][$date->format('Y-m-d')] = $arrDayInYear[$keyMonth][$date->format('Y-m-d')] + $dateTermination;
            }
            //Salary employee in month
            $result['salary'][$keyMonth] = array_sum($employees->map(function ($employee) use ($salarySlip, $month) {
                $salaryJson = 0;
                $salary = $salarySlip->where('employee_id', '=', $employee->id)
                    ->where('salary_from', Carbon::parse($month['start'])->format('Y-m-d').' 00:00:00')
                    ->where('salary_to', Carbon::parse($month['end'])->format('Y-m-d').' 00:00:00')
                    ->first();
                if ($salary) {
                    $salaryJson = !empty($salary->salary_json) ? $salary->salary_json['salary_convert'] : 0;
                }

                return $salaryJson / 1000000;
            })->toArray());
            //component id = 1 Salary supplements (PPL,...)
            $result['salary_supplements'][$keyMonth] = array_sum($employees->map(function ($employee) use ($salarySlip, $month) {
                $salaryJson = 0;
                $salary = $salarySlip->where('employee_id', '=', $employee->id)
                    ->where('salary_from', Carbon::parse($month['start'])->format('Y-m-d').' 00:00:00')
                    ->where('salary_to', Carbon::parse($month['end'])->format('Y-m-d').' 00:00:00')
                    ->first();
                if ($salary) {
                    foreach ($salary->salary_json['main_allowances'] as $key => $value) {
                        if ($value['component_id'] == 1) {
                            $salaryJson = floatval(str_replace(',', '', $value['convert_value']));
                        }
                    }
                }

                return $salaryJson / 1000000;
            })->toArray());
            //component id = 2 Salary Other income is paid in cash
            $result['salary_other_income'][$keyMonth] = array_sum($employees->map(function ($employee) use ($salarySlip, $month) {
                $salaryJson = 0;
                $salary = $salarySlip->where('employee_id', '=', $employee->id)
                    ->where('salary_from', Carbon::parse($month['start'])->format('Y-m-d').' 00:00:00')
                    ->where('salary_to', Carbon::parse($month['end'])->format('Y-m-d').' 00:00:00')
                    ->first();
                if ($salary) {
                    foreach ($salary->salary_json['main_allowances'] as $value) {
                        if ($value['component_id'] == 2) {
                            $salaryJson = floatval(str_replace(',', '', $value['convert_value']));
                        }
                    }
                }

                return $salaryJson / 1000000;
            })->toArray());
            //component id = 4 Salary Remuneration
            $result['salary_remuneration'][$keyMonth] = array_sum($employees->map(function ($employee) use ($salarySlip, $month) {
                $salaryJson = 0;
                $salary = $salarySlip->where('employee_id', '=', $employee->id)
                    ->where('salary_from', Carbon::parse($month['start'])->format('Y-m-d').' 00:00:00')
                    ->where('salary_to', Carbon::parse($month['end'])->format('Y-m-d').' 00:00:00')
                    ->first();
                if ($salary) {
                    foreach ($salary->salary_json['main_allowances'] as $value) {
                        if ($value['component_id'] == 3) {
                            $salaryJson = floatval(str_replace(',', '', $value['convert_value']));
                        }
                    }
                }

                return $salaryJson / 1000000;
            })->toArray());
        }

        for ($month = 1; $month <= 12; $month++) {
            $total = array_sum($arrDayInYear[$month]);
            $count = count($arrDayInYear[$month]);
            $average = ($count > 0) ? round($total / $count, 2) : 0;

            $result['avg_month'][$month] = $average;
        }

        return $result;
    }

    public function employeeUpInYear($year, $positionType)
    {
        $employee = Employee::query()
            ->withoutGlobalScope('active')
            ->with([
                'branch',
                'terminationAllowances',
                'transfers',
            ])
            ->where('employees.position_type', '=', $positionType)
            ->where('employees.branch_id', auth()->user()->branch_id)
            ->get();
        $result['count_manager'] = $employee
            ->where('date_to_company', '>=', Carbon::createFromFormat('Y', $year)->startOfYear()->format('Y-m-d'))
            ->where('date_to_company', '<=', Carbon::createFromFormat('Y', $year)->endOfYear()->format('Y-m-d'))
            ->count();
        $terminations = EmployeeTerminationAllowance::query()
            ->whereIn('employee_id', $employee->pluck('id')->toArray())
            ->where('termination_date', '>=', Carbon::createFromFormat('Y', $year)->startOfYear()->format('Y-m-d'))
            ->where('termination_date', '<=', Carbon::createFromFormat('Y', $year)->endOfYear()->format('Y-m-d'))
            ->get();
        $transfers = EmployeeTransfer::query()
            ->whereIn('employee_id', $employee->pluck('id')->toArray())
            ->where('transfer_date', '>=', Carbon::createFromFormat('Y', $year)->startOfYear()->format('Y-m-d'))
            ->where('transfer_date', '<=', Carbon::createFromFormat('Y', $year)->endOfYear()->format('Y-m-d'))
            ->get();
        $ot = Overtime::query()
            ->whereIn('employee_id', $employee->pluck('id')->toArray())
            ->where('overtime_date', '>=', Carbon::createFromFormat('Y', $year)->startOfYear()->format('Y-m-d'))
            ->where('overtime_date', '<=', Carbon::createFromFormat('Y', $year)->endOfYear()->format('Y-m-d'))
            ->get();
        $employeeDown = [];
        for ($i = 1; $i <= 12; $i++) {
            $result['employee_up'][] = $employee->where('date_to_company', '>=', Carbon::createFromFormat('Y-m', $year.'-'.$i)->startOfMonth()->format('Y-m-d'))
                ->where('date_to_company', '<=', Carbon::createFromFormat('Y-m', $year.'-'.$i)->endOfMonth()->format('Y-m-d'))
                ->count();
            $employeeDown['terminations'][] = $terminations->whereBetween('termination_date', [Carbon::createFromFormat('Y-m', $year.'-'.$i)->startOfMonth()->format('Y-m-d'), Carbon::createFromFormat('Y-m', $year.'-'.$i)->endOfMonth()->format('Y-m-d')])->count();
            $employeeDown['transfers'][] = $transfers->whereBetween('transfer_date', [Carbon::createFromFormat('Y-m', $year.'-'.$i)->startOfMonth()->format('Y-m-d'), Carbon::createFromFormat('Y-m', $year.'-'.$i)->endOfMonth()->format('Y-m-d')])->count();
            $result['employee_ot'][] = $ot->whereBetween('overtime_date', [Carbon::createFromFormat('Y-m', $year.'-'.$i)->startOfMonth()->format('Y-m-d'), Carbon::createFromFormat('Y-m', $year.'-'.$i)->endOfMonth()->format('Y-m-d')])->sum('total_amount') / 1000000;
        }
        foreach ($employeeDown['terminations'] as $key => $value) {
            $result['employee_down'][$key] = $employeeDown['transfers'][$key] + $value;
        }

        return $result;
    }

    public function getReportSalary(array $params)
    {
        $branchId = empty(auth()->user()->branch_id) ? Branch::all()->pluck('id')->toArray() : [auth()->user()->branch_id];
        $positionType = data_get($params, 'filters.position_type', 'manager');
        $year = data_get($params, 'filters.year', date('Y'));
        $dataCurrentYear = $this->resultByYear($branchId, $year, $positionType);
        $dataLastYear = $this->resultByYear($branchId, $year - 1, $positionType);

        $result['current'] = $dataCurrentYear;
        $result['last'] = $dataLastYear;
        $result['avg_month'] = round(array_sum($dataCurrentYear['avg_month']) / 12, 2);
        $result['salary'] = round(array_sum($dataCurrentYear['salary']) / 12, 2);
        $result['salary_supplements'] = round(array_sum($dataCurrentYear['salary_supplements']) / 12, 2);
        $result['salary_other_income'] = round(array_sum($dataCurrentYear['salary_other_income']) / 12, 2);
        $result['salary_remuneration'] = round(array_sum($dataCurrentYear['salary_remuneration']) / 12, 2);
        $result['last_employee_up'] = $this->employeeUpInYear($year - 1, $positionType)['count_manager'];
        $result['last_employee_down'] = $this->employeeUpInYear($year - 1, $positionType)['count_manager'];
        $result['current_employee_up'] = $this->employeeUpInYear($year, $positionType)['employee_up'];
        $result['current_employee_down'] = $this->employeeUpInYear($year, $positionType)['employee_down'];
        $result['employee_ot'] = $this->employeeUpInYear($year, $positionType)['employee_ot'];

        return $result;
    }

    public function employeeStatistic(array $params)
    {
        $employee = $this->employeeRepository->withoutGlobalScope('active')->where('branch_id', auth()->user()->branch_id)->get();
        $data['total_employee'] = $employee->where('type', '<>', Employee::TYPE_REMOVAL)
            ->where('date_to_company', '>=', $params['year'].'-01-01')
            ->where('date_to_company', '<=', $params['year'].'-12-31')->count();
        $data['total_manager'] = $employee->where('position_type', Employee::POSITION_TYPE_MANAGER)
            ->where('date_to_company', '>=', $params['year'].'-01-01')
            ->where('date_to_company', '<=', $params['year'].'-12-31')->count();
        $data['total_employee_contractor'] = $employee->where('type', Employee::TYPE_CONTRACTOR)
            ->where('date_to_company', '>=', $params['year'].'-01-01')
            ->where('date_to_company', '<=', $params['year'].'-12-31')->count();
        $data['total_employee_removal'] = EmployeeTerminationAllowance::query()
            ->where('termination_date', '>=', $params['year'].'-01-01')
            ->where('termination_date', '<=', $params['year'].'-12-31')->count();
        $data['total_employee_staff'] = $employee->where('type', Employee::TYPE_STAFF)
            ->where('date_to_company', '>=', $params['year'].'-01-01')
            ->where('date_to_company', '<=', $params['year'].'-12-31')->count();

        return $data;
    }

    public function getReportSalaries(array $params)
    {
        return (new PayrollService())->getPayslips($params);
    }
}
