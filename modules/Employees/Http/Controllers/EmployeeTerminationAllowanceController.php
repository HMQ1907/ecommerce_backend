<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employees\Http\Requests\EmployeeTerminationCreateRequest;
use Modules\Employees\Models\EmployeeTerminationAllowance;
use Modules\Employees\Services\EmployeeTerminationService;
use Modules\Employees\Transformers\EmployeeTerminationAllowanceTransformer;

class EmployeeTerminationAllowanceController extends Controller
{
    protected $employeeTerminationService;

    public function __construct(EmployeeTerminationService $employeeTerminationService)
    {
        $this->employeeTerminationService = $employeeTerminationService;
        $this->authorizeResource(EmployeeTerminationAllowance::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->employeeTerminationService->getEmployeeTerminations($request->all());

        return responder()->success($data, EmployeeTerminationAllowanceTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EmployeeTerminationCreateRequest $request)
    {
        $data = $this->employeeTerminationService->createEmployeeTermination($request->all());

        return responder()->success($data, EmployeeTerminationAllowanceTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(EmployeeTerminationAllowance $employee_termination)
    {
        $data = $this->employeeTerminationService->getEmployeeTermination($employee_termination->id);

        return responder()->success($data, EmployeeTerminationAllowanceTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EmployeeTerminationCreateRequest $request, EmployeeTerminationAllowance $employee_termination)
    {
        $data = $this->employeeTerminationService->editEmployeeTermination($employee_termination->id, $request->all());

        return responder()->success($data, EmployeeTerminationAllowanceTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(EmployeeTerminationAllowance $employee_termination)
    {
        $this->employeeTerminationService->deleteEmployeeTermination($employee_termination->id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->employeeTerminationService->deleteEmployeeTerminations($request->ids);

        return responder()->success()->respond();
    }
}
