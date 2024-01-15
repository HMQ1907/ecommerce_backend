<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kjmtrue\VietnamZone\Models\District;
use Kjmtrue\VietnamZone\Models\Province;
use Kjmtrue\VietnamZone\Models\Ward;

class PublicController extends Controller
{
    public function getProvinces(Request $request)
    {
        $provinces = Province::where('name', 'LIKE', "%{$request->q}%")->get();

        return responder()->success($provinces)->respond();
    }

    public function getDistricts(Request $request)
    {
        $districts = District::whereProvinceId($request->province_id)->where('name', 'LIKE', "%{$request->q}%")->get();

        return responder()->success($districts)->respond();
    }

    public function getWards(Request $request)
    {
        $wards = Ward::whereDistrictId($request->district_id)->where('name', 'LIKE', "%{$request->q}%")->get();

        return responder()->success($wards)->respond();
    }
}
