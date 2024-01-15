<?php

namespace Modules\Designations\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Designations\Http\Requests\DesignationRequest;
use Modules\Designations\Models\Designation;
use Modules\Designations\Services\DesignationService;
use Modules\Designations\Transformers\DesignationTransformer;

class DesignationsController extends Controller
{
    protected $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
        $this->authorizeResource(Designation::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->designationService->getDesignations($request->all());

        return responder()->success($data, DesignationTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(DesignationRequest $request)
    {
        $data = $this->designationService->createDesignation($request->all());

        return responder()->success($data, DesignationTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show(Designation $designation)
    {
        $data = $this->designationService->getDesignation($designation->id);

        return responder()->success($data, DesignationTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(DesignationRequest $request, Designation $designation)
    {
        $data = $this->designationService->updateDesignation($designation->id, $request->all());

        return responder()->success($data, DesignationTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(Designation $designation)
    {
        $this->designationService->deleteDesignation($designation->id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->designationService->deleteDesignations($request->ids);

        return responder()->success()->respond();
    }
}
