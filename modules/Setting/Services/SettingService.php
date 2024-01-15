<?php

namespace Modules\Setting\Services;

use App\Services\BaseService;
use App\Settings\AttendanceSettings;
use App\Settings\ConfigSettings;
use App\Settings\DeliverySettings;
use App\Settings\LeaveSettings;
use Illuminate\Support\Facades\DB;

class SettingService extends BaseService
{
    public function getShippingDiscount()
    {
        try {
            $configSetting = app(ConfigSettings::class);

            return [
                'shipping_discount' => $configSetting->shipping_discount,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createShippingDiscount($params)
    {
        try {
            DB::beginTransaction();

            $configSetting = app(ConfigSettings::class);
            $configSetting->shipping_discount = $params['shipping_discount'];
            $configSetting->save();

            DB::commit();

            return $configSetting;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getLeaveLimit()
    {
        try {
            $configSetting = app(LeaveSettings::class);

            return [
                'limit' => $configSetting->limit,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createLeaveLimit($params)
    {
        try {
            DB::beginTransaction();

            $configSetting = app(LeaveSettings::class);
            $configSetting->limit = $params['limit'];
            $configSetting->save();

            DB::commit();

            return $configSetting;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getDeliveryPrice()
    {
        try {
            $deliverySettings = app(DeliverySettings::class);

            return [
                'prices' => $deliverySettings->prices,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createDeliveryPrice(array $params)
    {
        try {
            DB::beginTransaction();

            $deliverySettings = app(DeliverySettings::class);
            $deliverySettings->prices = $params['prices'];
            $deliverySettings->save();

            DB::commit();

            return $deliverySettings;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getTimeLocation()
    {
        try {
            $attendanceSettings = app(AttendanceSettings::class);

            return [
                'lat' => $attendanceSettings->lat,
                'lng' => $attendanceSettings->lng,
                'radius' => $attendanceSettings->radius,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateTimeLocation($params)
    {
        try {
            DB::beginTransaction();

            $attendanceSettings = app(AttendanceSettings::class);
            $attendanceSettings->lat = $params['lat'];
            $attendanceSettings->lng = $params['lng'];
            $attendanceSettings->radius = $params['radius'];
            $attendanceSettings->save();

            DB::commit();

            return $attendanceSettings;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getWifiAddress()
    {
        try {
            $attendanceSettings = app(AttendanceSettings::class);

            return [
                'ips' => explode(',', $attendanceSettings->ips),
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateWifiAddress($params)
    {
        try {
            DB::beginTransaction();

            $wifiAddressSetting = app(AttendanceSettings::class);
            $wifiAddressSetting->ips = $params['ips'];
            $wifiAddressSetting->save();

            DB::commit();

            return $wifiAddressSetting;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getWorkTime()
    {
        try {
            $attendanceSettings = app(AttendanceSettings::class);

            return [
                'work_time' => $attendanceSettings->work_time,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateWorkTime(array $params)
    {
        try {
            DB::beginTransaction();

            $attendanceSettings = app(AttendanceSettings::class);
            $attendanceSettings->work_time = $params['work_time'];
            $attendanceSettings->save();

            DB::commit();

            return $attendanceSettings;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
