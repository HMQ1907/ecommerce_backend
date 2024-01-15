<?php

namespace Modules\Overtimes\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Overtimes\Http\Requests\CreateOvertimeRequest;
use Modules\Overtimes\Models\Overtime;
use Modules\Overtimes\Services\OvertimeService;
use Modules\Overtimes\Transformers\OvertimeTransformer;

class OvertimesController extends Controller
{
    protected $overtimeService;

    public function __construct(OvertimeService $overtimeService)
    {
        $this->overtimeService = $overtimeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->overtimeService->getOvertimes($request->all());

        return responder()->success($data, OvertimeTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateOvertimeRequest $request)
    {
        $data = $this->overtimeService->createOvertime($request->all());

        return responder()->success($data, OvertimeTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Overtime $overtime)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateOvertimeRequest $request, $id)
    {
        $data = $this->overtimeService->editOvertime($id, $request->all());

        return responder()->success($data, OvertimeTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Overtime $overtime)
    {
        $this->overtimeService->deleteOvertime($overtime->id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->overtimeService->deleteOvertimes($request->ids);

        return responder()->success()->respond();
    }
}
