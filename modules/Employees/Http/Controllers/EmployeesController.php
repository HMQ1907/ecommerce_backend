<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\CompletedExportJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Branches\Models\Branch;
use Modules\Employees\Exports\NewPositionExport;
use Modules\Employees\Exports\SalaryReportExport;
use Modules\Employees\Exports\TradingReportExport;
use Modules\Employees\Http\Requests\BankAccountUpdateRequest;
use Modules\Employees\Http\Requests\CompanyUpdateRequest;
use Modules\Employees\Http\Requests\EmployeeCreateRequest;
use Modules\Employees\Http\Requests\EmployeeUpdateRequest;
use Modules\Employees\Http\Requests\PersonalUpdateRequest;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\Retaliation;
use Modules\Employees\Services\EmployeeService;
use Modules\Employees\Transformers\EmployeeTransformer;

class EmployeesController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
        $this->authorizeResource(Employee::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->employeeService->getEmployees($request->all());

        return responder()->success($data, EmployeeTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EmployeeCreateRequest $request)
    {
        $data = $this->employeeService->createEmployee($request->all(), $request->file('avatar'));

        return responder()->success($data, EmployeeTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Employee $employee)
    {
        $data = $this->employeeService->getEmployee($employee->id);

        return responder()->success($data, EmployeeTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EmployeeUpdateRequest $request, Employee $employee)
    {
        $data = $this->employeeService->editEmployee($employee->id, $request->all(), $request->file('avatar'));

        return responder()->success($data, EmployeeTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Employee $employee)
    {
        $this->employeeService->deleteEmployee($employee->id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->employeeService->deleteEmployees($request->ids);

        return responder()->success()->respond();
    }

    public function sendMessage(Request $request, $id)
    {
        $this->employeeService->sendMessage($id, $request->all());

        return responder()->success()->respond();
    }

    public function getSalesWorkingMonth(Request $request)
    {
        $data = $this->employeeService->getSalesWorkingMonth($request->all());

        return responder()->success($data)->respond();
    }

    public function getEmployeeWorkingMonth(Request $request)
    {
        $data = $this->employeeService->getEmployeeWorkingMonth($request->all());

        return responder()->success($data)->respond();
    }

    public function updatePersonal(PersonalUpdateRequest $request, $id)
    {
        $data = $this->employeeService->updatePersonal($id, $request->all());

        return responder()->success($data, EmployeeTransformer::class)->respond();
    }

    public function updateBankAccount(BankAccountUpdateRequest $request, $id)
    {
        $data = $this->employeeService->updateBankAccount($id, $request->all());

        return responder()->success($data, EmployeeTransformer::class)->respond();
    }

    public function updateCompany(CompanyUpdateRequest $request, $id)
    {
        $data = $this->employeeService->updateCompany($id, $request->all());

        return responder()->success($data, EmployeeTransformer::class)->respond();
    }

    public function exportReportSalary(Request $request)
    {
        $fileName = now()->format('Y-m-d-h:i:s').'-Lao BCQL.xlsx';
        $year = data_get($request->all(), 'filters.year', now()->year);
        $data = $this->employeeService->getReportSalary($request->all());

        return Excel::download(new SalaryReportExport($data, $year), $fileName);
    }

    public function reportSalary(Request $request)
    {
        $data = $this->employeeService->getReportSalary($request->all());

        return responder()->success($data)->respond();
    }

    public function employeeStatistic(Request $request)
    {
        $data = $this->employeeService->employeeStatistic($request->all());

        return responder()->success($data)->respond();
    }

    public function reportSalaries(Request $request)
    {
        try {
            $data = $this->employeeService->getReportSalaries($request->all());
            $fileName = now()->format('H_i_s').'Report-Salary.xlsx';

            // return Excel::download(new TradingReportExport($data, $request->all()), $fileName);
            Excel::queue(new TradingReportExport($data, $request->all()), 'exports/'.now()->format('H-i-s').'-'.$fileName, 'public')->chain([
                new CompletedExportJob(auth()->user(), 'exports/'.now()->format('H-i-s').'-'.$fileName),
            ]);

            return responder()->success()->respond();
        } catch (\Exception $e) {
            return responder()->error($e->getMessage())->respond(500);
        }
    }

    public function reportRetaliations(Request $request)
    {
        try {
            $params = $request->all();
            $applySalaryMonth = data_get($params, 'filter.apply_salary_month');
            $incrementMonth = data_get($params, 'filter.increment_month');

            $data = Retaliation::query()
                ->with(['employee' => function ($query) {
                    $query->select('id', 'first_name', 'type', 'last_name', 'gender', DB::raw("CONCAT(first_name, ' ', last_name) as full_name"));
                }, 'employee.bankAccounts' => function ($query) {
                    $query->where('bank_name', 'LDB')
                        ->orderByDesc('created_at');
                }])
                ->select('retaliations.*',
                    DB::raw('(retaliations.new_salary - retaliations.previous_salary) as amount'),
                    DB::raw('TIMESTAMPDIFF(MONTH, retaliations.apply_salary_date, retaliations.increment_date) + 1 as months')
                )
                ->when(isset($applySalaryMonth), function ($query) use ($applySalaryMonth) {
                    return $query->whereMonth('retaliations.apply_salary_date', Carbon::parse($applySalaryMonth)->format('m'))
                        ->whereYear('retaliations.apply_salary_date', Carbon::parse($applySalaryMonth)->format('Y'));
                })
                ->when(isset($incrementMonth), function ($query) use ($incrementMonth) {
                    return $query->whereMonth('retaliations.increment_date', Carbon::parse($incrementMonth)->format('m'))
                        ->whereYear('retaliations.increment_date', Carbon::parse($incrementMonth)->format('Y'));
                })
                ->when(isset($applySalaryMonth) && isset($incrementMonth), function ($query) use ($applySalaryMonth, $incrementMonth) {
                    return $query
                        ->whereMonth('retaliations.apply_salary_date', Carbon::parse($applySalaryMonth)->format('m'))
                        ->whereYear('retaliations.apply_salary_date', Carbon::parse($applySalaryMonth)->format('Y'))
                        ->whereMonth('retaliations.increment_date', Carbon::parse($incrementMonth)->format('m'))
                        ->whereYear('retaliations.increment_date', Carbon::parse($incrementMonth)->format('Y'));
                })
                ->get();

            $branchName = Branch::query()->find(auth()->user()->branch_id)->name;
            $fileName = now()->format('H_i_s').'Retaliations.xlsx';

            return Excel::download(new NewPositionExport($data, $branchName), $fileName);
        } catch (\Exception $e) {
            return responder()->error($e->getMessage())->respond(500);
        }
    }
}
