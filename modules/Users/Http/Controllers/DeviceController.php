<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Users\Http\Requests\DeviceRequest;
use Modules\Users\Services\DeviceService;

class DeviceController extends Controller
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DeviceRequest $request)
    {
        $device = $this->deviceService->createDevice($request->all());

        return responder()->success($device)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeviceRequest $request)
    {
        $device = $this->deviceService->deleteDevice($request->all());

        return responder()->success($device)->respond();
    }
}
