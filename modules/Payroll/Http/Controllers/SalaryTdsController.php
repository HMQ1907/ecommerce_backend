<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payroll\Http\Requests\SalaryTDSRequest;
use Modules\Payroll\Services\SalaryTdsService;
use Modules\Payroll\Transformers\SalaryTdsTransformer;

class SalaryTdsController extends Controller
{
    protected $salaryTdsService;

    public function __construct(SalaryTdsService $salaryTdsService)
    {
        $this->salaryTdsService = $salaryTdsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->salaryTdsService->getSalaryTDSs($request->all());

        return responder()->success($data, SalaryTdsTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SalaryTdsRequest $request)
    {
        $data = $this->salaryTdsService->createSalaryTDS($request->all());

        return responder()->success($data, SalaryTdsTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SalaryTdsRequest $request, $id)
    {
        $data = $this->salaryTdsService->editSalaryTDS($id, $request->all());

        return responder()->success($data, SalaryTdsTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->salaryTdsService->deleteSalaryTDS($id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->salaryTdsService->deleteSalaryTDSs($request->ids);

        return responder()->success()->respond();
    }
}
