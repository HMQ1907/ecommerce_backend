<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payroll\Services\SalaryGroupService;
use Modules\Payroll\Transformers\SalaryGroupTransformer;

class SalaryGroupsController extends Controller
{
    protected $salaryGroupService;

    public function __construct(SalaryGroupService $salaryGroupService)
    {
        $this->salaryGroupService = $salaryGroupService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->salaryGroupService->getSalaryGroups($request->all());

        return responder()->success($data, SalaryGroupTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->salaryGroupService->createSalaryGroup($request->all());

        return responder()->success($data, SalaryGroupTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->salaryGroupService->getSalaryGroup($id);

        return responder()->success($data, SalaryGroupTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $this->salaryGroupService->editSalaryGroup($request->all(), $id);

        return responder()->success($data, SalaryGroupTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->salaryGroupService->deleteSalaryGroup($id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->salaryGroupService->deleteSalaryGroups($request->ids);

        return responder()->success()->respond();
    }

    public function assign(Request $request, $id)
    {
        $this->salaryGroupService->assign($id, $request->all());

        return responder()->success()->respond();
    }
}
