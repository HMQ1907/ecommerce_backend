<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employees\Http\Requests\EmployeeTransferCreateRequest;
use Modules\Employees\Http\Requests\EmployeeTransferUpdateRequest;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeTransfer;
use Modules\Employees\Services\EmployeeTransferService;
use Modules\Employees\Transformers\EmployeeTransferTransformer;

class EmployeeTransfersController extends Controller
{
    protected $employeeTransferService;

    public function __construct(EmployeeTransferService $employeeTransferService)
    {
        $this->employeeTransferService = $employeeTransferService;
        $this->authorizeResource(EmployeeTransfer::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->employeeTransferService->getEmployeeTransfers($request->all());

        return responder()->success($data, EmployeeTransferTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EmployeeTransferCreateRequest $request)
    {
        $data = $this->employeeTransferService->createEmployeeTransfer($request->all());

        return responder()->success($data, EmployeeTransferTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Employee $employee)
    {
        $data = $this->employeeTransferService->getEmployeeTransfer($employee->id);

        return responder()->success($data, EmployeeTransferTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EmployeeTransferUpdateRequest $request, $id)
    {
        $data = $this->employeeTransferService->editEmployeeTransfer($id, $request->all());

        return responder()->success($data, EmployeeTransferTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->employeeTransferService->deleteEmployeeTransfer($id);

        return responder()->success()->respond();
    }
}
