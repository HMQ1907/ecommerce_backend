<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payroll\Http\Requests\SalaryComponentRequest;
use Modules\Payroll\Services\SalaryComponentService;
use Modules\Payroll\Transformers\SalaryComponentTransformer;

class SalaryComponentsController extends Controller
{
    protected $salaryComponentService;

    public function __construct(SalaryComponentService $salaryComponentService)
    {
        $this->salaryComponentService = $salaryComponentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->salaryComponentService->getSalaryComponents($request->all());

        return responder()->success($data, SalaryComponentTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SalaryComponentRequest $request)
    {
        $data = $this->salaryComponentService->createSalaryComponent($request->all());

        return responder()->success($data, SalaryComponentTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->salaryComponentService->getSalaryComponent($id);

        return responder()->success($data, SalaryComponentTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SalaryComponentRequest $request, $id)
    {
        $data = $this->salaryComponentService->editSalaryComponent($id, $request->all());

        return responder()->success($data, SalaryComponentTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->salaryComponentService->deleteSalaryComponent($id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->salaryComponentService->deleteSalaryComponents($request->ids);

        return responder()->success()->respond();
    }

    public function getMainAllowances(Request $request)
    {
        $data = $this->salaryComponentService->getMainAllowances();

        return responder()->success($data)->respond();
    }
}
