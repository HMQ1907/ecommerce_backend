<?php

namespace Modules\Employees\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employees\Http\Requests\RetaliationCreateRequest;
use Modules\Employees\Http\Requests\RetaliationUpdateRequest;
use Modules\Employees\Models\Retaliation;
use Modules\Employees\Services\RetaliationService;
use Modules\Employees\Transformers\RetaliationTransformer;

class RetaliationsController extends Controller
{
    protected $retaliationService;

    public function __construct(RetaliationService $retaliationService)
    {
        $this->retaliationService = $retaliationService;
        $this->authorizeResource(Retaliation::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->retaliationService->getRetaliations($request->all());

        return responder()->success($data, RetaliationTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RetaliationCreateRequest $request)
    {
        $data = $this->retaliationService->createRetaliation($request->all());

        return responder()->success($data, RetaliationTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Retaliation $employeeRetaliation)
    {
        $data = $this->retaliationService->getRetaliation($employeeRetaliation->id);

        return responder()->success($data, RetaliationTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RetaliationUpdateRequest $request, Retaliation $employeeRetaliation)
    {
        $data = $this->retaliationService->editRetaliation($employeeRetaliation->id, $request->all());

        return responder()->success($data, RetaliationTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Retaliation $employeeRetaliation)
    {
        $this->retaliationService->deleteRetaliation($employeeRetaliation->id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->retaliationService->deleteRetaliations($request->ids);

        return responder()->success()->respond();
    }
}
