<?php

namespace Modules\Users\Services;

use App\Services\BaseService;

class DeviceService extends BaseService
{
    public function createDevice(array $params)
    {
        try {
            $data = auth()->user()->devices()->firstWhere('token', $params['token']);
            if ($data) {
                $data->touch();
            } else {
                $data = auth()->user()->devices()->create($params);
            }

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function deleteDevice(array $params)
    {
        try {
            $data = auth()->user()->devices()->firstWhere('token', $params['token']);
            optional($data)->delete();

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
