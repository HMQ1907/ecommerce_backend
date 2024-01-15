<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employees\Http\Requests\EmployeeContractCreateRequest;
use Modules\Employees\Http\Requests\EmployeeContractUpdateRequest;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\EmployeeContract;
use Modules\Employees\Services\EmployeeContractService;
use Modules\Employees\Transformers\EmployeeContractFileTransformer;
use Modules\Employees\Transformers\EmployeeContractTransformer;

class EmployeeContractsController extends Controller
{
    protected $employeeContractService;

    public function __construct(EmployeeContractService $employeeContractService)
    {
        $this->employeeContractService = $employeeContractService;
        $this->authorizeResource(EmployeeContract::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->employeeContractService->getEmployeeContracts($request->all());

        return responder()->success($data, EmployeeContractTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EmployeeContractCreateRequest $request)
    {
        $data = $this->employeeContractService->createEmployeeContract($request->all());

        return responder()->success($data, EmployeeContractTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Employee $employee)
    {
        $data = $this->employeeContractService->getEmployeeContract($employee->id);

        return responder()->success($data, EmployeeContractTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EmployeeContractUpdateRequest $request, $id)
    {
        $data = $this->employeeContractService->editEmployeeContract($id, $request->all());

        return responder()->success($data, EmployeeContractTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Employee $employee)
    {
        $this->employeeContractService->deleteEmployeeContract($employee->id);

        return responder()->success()->respond();
    }

    public function getEmployeeContractByEmployeeId($employeeId)
    {
        $data = $this->employeeContractService->getEmployeeContractByEmployeeId($employeeId);

        return responder()->success($data, EmployeeContractTransformer::class)->respond();
    }

    public function getFiles(Request $request, $id)
    {
        $data = $this->employeeContractService->getFiles($id, $request->all());

        return responder()->success($data, EmployeeContractFileTransformer::class)->respond();
    }

    public function deleteFile($id, $fileId)
    {
        $this->employeeContractService->deleteFile($id, $fileId);

        return responder()->success()->respond();
    }

    public function getEmployeeContractByType(Request $request)
    {
        $data = $this->employeeContractService->getEmployeeContractByType($request->contract_type);

        return responder()->success($data, EmployeeContractTransformer::class)->respond();
    }
}
