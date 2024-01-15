<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employees\Exports\EmployeeAwardsExport;
use Modules\Employees\Http\Requests\EmployeeAwardCreateRequest;
use Modules\Employees\Models\Award;
use Modules\Employees\Services\EmployeeAwardService;
use Modules\Employees\Transformers\AwardTransformer;
use Modules\Employees\Transformers\EmployeeAwardTransformer;

class EmployeeAwardsController extends Controller
{
    protected $employeeAwardService;

    public function __construct(EmployeeAwardService $employeeAwardService)
    {
        $this->employeeAwardService = $employeeAwardService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->employeeAwardService->getEmployeeAwards($request->all());

        return responder()->success($data, AwardTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EmployeeAwardCreateRequest $request)
    {
        $data = $this->employeeAwardService->createEmployeeAward($request->all());

        return responder()->success($data, AwardTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Award $employee_award)
    {
        $data = $this->employeeAwardService->getEmployeeAward($employee_award->id);

        return responder()->success($data, AwardTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Award $employee_award)
    {
        $data = $this->employeeAwardService->editEmployeeAward($request->all(), $employee_award->id);

        return responder()->success($data, AwardTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Award $employee_award)
    {
        $data = $this->employeeAwardService->deleteAward($employee_award->id);

        return responder()->success($data)->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $data = $this->employeeAwardService->deleteAwards($request->ids);

        return responder()->success($data)->respond();
    }

    public function deleteEmployeeAward($id)
    {
        $data = $this->employeeAwardService->deleteEmployeeAward($id);

        return responder()->success($data)->respond();
    }

    public function bulkDeleteEmployeeAward(Request $request)
    {
        $data = $this->employeeAwardService->deleteEmployeeAwards($request->ids);

        return responder()->success($data)->respond();
    }

    public function getEmployeeOfAwards($id)
    {
        $data = $this->employeeAwardService->getEmployeeOfAwards($id);

        return responder()->success($data, EmployeeAwardTransformer::class)->respond();
    }

    public function exportEmployeeAward(Request $request)
    {
        $data = $this->employeeAwardService->exportEmployeeAward($request->all());

        return Excel::download(new EmployeeAwardsExport($data), now()->format('H_i_s').'employee_award.xlsx');
    }
}
