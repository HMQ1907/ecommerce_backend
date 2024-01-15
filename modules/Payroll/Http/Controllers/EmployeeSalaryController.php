<?php

namespace Modules\Payroll\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Departments\Services\DepartmentService;
use Modules\Payroll\Exports\LaoRecordExport;
use Modules\Payroll\Services\EmployeeSalaryService;
use Modules\Payroll\Services\PayrollService;
use Modules\Payroll\Transformers\EmployeeSalaryTransformer;

class EmployeeSalaryController extends Controller
{
    protected $employeeSalaryService;

    protected $departmentService;

    public function __construct(EmployeeSalaryService $employeeSalaryService, DepartmentService $departmentService)
    {
        $this->employeeSalaryService = $employeeSalaryService;
        $this->departmentService = $departmentService;
        $this->middleware('permission:employee_salaries.view')->only(['index', 'show']);
        $this->middleware('permission:employee_salaries.create')->only('store');
        $this->middleware('permission:employee_salaries.edit')->only('update');
        $this->middleware('permission:employee_salaries.delete')->only(['destroy', 'bulkDestroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->employeeSalaryService->getEmployeeSalaries($request->all());

        return responder()->success($data, EmployeeSalaryTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->employeeSalaryService->createEmployeeSalary($request->all());

        return responder()->success($data)->respond();
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        $data = $this->employeeSalaryService->getEmployeeSalary($id, $request->all());

        return responder()->success($data)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $this->employeeSalaryService->updateEmployeeSalary($request->all(), $id);

        return responder()->success($data)->respond();
    }

    public function getEmployeeSalaryGroup($id)
    {
        $data = $this->employeeSalaryService->getEmployeeSalaryGroup($id);

        return responder()->success($data)->respond();
    }

    public function reportLaoRecord(Request $request)
    {
        $data = $this->employeeSalaryService->reportLaoRecord($request->all());
        $date = explode(',', data_get($request, 'filter.start_date_between'));
        $enum = [
            'branch_id' => auth()->user()->branch_id,
            'date' => Carbon::parse(data_get($request, 'filter.month'))->format('d-M-Y'),
            'date_start' => $date[0] ?? null,
            'date_end' => $date[1] ?? null,
        ];
        $dataGender = $this->departmentService->exportGenderDepartment($request->all());
        $total = [
            'female_expatriate_count' => $dataGender->sum('female_expatriate_count'),
            'male_expatriate_count' => $dataGender->sum('male_expatriate_count'),
            'female_contract_count' => $dataGender->sum('female_contract_count'),
            'male_contract_count' => $dataGender->sum('male_contract_count'),
            'female_staff_count' => $dataGender->sum('female_staff_count'),
            'male_staff_count' => $dataGender->sum('male_staff_count'),
        ];
        $params = $request->all();
        unset($params['filter']['month']);
        $request->replace($params);
        $dataRemoval = (new PayrollService())->getPayslips($params);

        $newEmployee = $this->employeeSalaryService->getNewEmployee($request->all());

        return Excel::download(new LaoRecordExport($data, $enum, $dataGender, $total, $dataRemoval, $newEmployee), now()->format('H_i_s').'record.xlsx');
    }
}
