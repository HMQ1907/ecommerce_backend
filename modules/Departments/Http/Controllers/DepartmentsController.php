<?php

namespace Modules\Departments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Branches\Models\Branch;
use Modules\Departments\Exports\GenderDepartmentExport;
use Modules\Departments\Exports\LaborPlanExport;
use Modules\Departments\Exports\SalaryDepartmentExport;
use Modules\Departments\Http\Requests\DepartmentCreateRequest;
use Modules\Departments\Http\Requests\DepartmentUpdateRequest;
use Modules\Departments\Models\Department;
use Modules\Departments\Services\DepartmentService;
use Modules\Departments\Transformers\DepartmentEmployeeTransformer;
use Modules\Departments\Transformers\DepartmentTransformer;

class DepartmentsController extends Controller
{
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
        $this->authorizeResource(Department::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->departmentService->getDepartments($request->all());

        return responder()->success($data, DepartmentEmployeeTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DepartmentCreateRequest $request)
    {
        $data = $this->departmentService->createDepartment($request->all());

        return responder()->success($data, DepartmentTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Department $department)
    {
        $data = $this->departmentService->getDepartment($department->id);

        return responder()->success($data, DepartmentTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DepartmentUpdateRequest $request, Department $department)
    {
        $data = $this->departmentService->editDepartment($department->id, $request->all());

        return responder()->success($data, DepartmentTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Department $department)
    {
        $this->departmentService->deleteDepartment($department->id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->departmentService->deleteDepartments($request->ids);

        return responder()->success()->respond();
    }

    public function deleteTeamInDepartment($id, $teamId)
    {
        $this->departmentService->deleteTeamInDepartment($id, $teamId);

        return responder()->success()->respond();
    }

    public function getOverviewChart(Request $request)
    {
        $data = $this->departmentService->getOverviewChart($request->all());

        return responder()->success($data, DepartmentTransformer::class)->respond();
    }

    public function exportSalary(Request $request)
    {
        $fileName = now()->format('Y-m-d-h:i:s').'-Salary-Report.xlsx';

        return Excel::download(new SalaryDepartmentExport(), $fileName);
    }

    public function export()
    {
        $fileName = now()->format('Y-m-d-h:i:s').'-DinhBienLD.xlsx';

        return Excel::download(new LaborPlanExport(), $fileName);
    }

    public function exportGender(Request $request)
    {
        $fileName = now()->format('Y-m-d-h:i:s').'-AmountGenderDepartment.xlsx';
        $branchName = Branch::query()->find(data_get($request, 'filters.branch_id'))->name;
        $data = $this->departmentService->exportGenderDepartment($request->all());
        $total = [
            'female_expatriate_count' => $data->sum('female_expatriate_count'),
            'male_expatriate_count' => $data->sum('male_expatriate_count'),
            'female_contract_count' => $data->sum('female_contract_count'),
            'male_contract_count' => $data->sum('male_contract_count'),
            'female_staff_count' => $data->sum('female_staff_count'),
            'male_staff_count' => $data->sum('male_staff_count'),
        ];

        return Excel::download(new GenderDepartmentExport($data, $total, $branchName), $fileName);
    }
}
