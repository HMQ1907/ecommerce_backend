<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Setting\Http\Requests\ConfigRequest;
use Modules\Setting\Http\Requests\DeliveryPriceRequest;
use Modules\Setting\Http\Requests\LeaveLimitRequest;
use Modules\Setting\Http\Requests\LocationRequest;
use Modules\Setting\Http\Requests\WifiAddressRequest;
use Modules\Setting\Http\Requests\WorkTimeRequest;
use Modules\Setting\Services\SettingService;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function getShippingDiscount()
    {
        $data = $this->settingService->getShippingDiscount();

        return responder()->success($data)->respond();
    }

    public function createShippingDiscount(ConfigRequest $configRequest)
    {
        $data = $this->settingService->createShippingDiscount($configRequest->all());

        return responder()->success($data)->respond();
    }

    public function getLeaveLimit()
    {
        $data = $this->settingService->getLeaveLimit();

        return responder()->success($data)->respond();
    }

    public function createLeaveLimit(LeaveLimitRequest $leaveLimitRequest)
    {
        $data = $this->settingService->createLeaveLimit($leaveLimitRequest->all());

        return responder()->success($data)->respond();
    }

    public function getDeliveryPrices()
    {
        $data = $this->settingService->getDeliveryPrice();

        return responder()->success($data)->respond();
    }

    public function saveDeliveryPrices(DeliveryPriceRequest $request)
    {
        $data = $this->settingService->createDeliveryPrice($request->all());

        return responder()->success($data)->respond();
    }

    public function getTimeLocation()
    {
        $data = $this->settingService->getTimeLocation();

        return responder()->success($data)->respond();
    }

    public function updateTimeLocation(LocationRequest $locationRequest)
    {
        $data = $this->settingService->updateTimeLocation($locationRequest->all());

        return responder()->success($data)->respond();
    }

    public function getWifiAddress()
    {
        $data = $this->settingService->getWifiAddress();

        return responder()->success($data)->respond();
    }

    public function updateWifiAddress(WifiAddressRequest $wifiAddressRequest)
    {
        $data = $this->settingService->updateWifiAddress($wifiAddressRequest->all());

        return responder()->success($data)->respond();
    }

    public function getWorkTime()
    {
        $data = $this->settingService->getWorkTime();

        return responder()->success($data)->respond();
    }

    public function saveWorkTime(WorkTimeRequest $workTimeRequest)
    {
        $data = $this->settingService->updateWorkTime($workTimeRequest->all());

        return responder()->success($data)->respond();
    }
}
