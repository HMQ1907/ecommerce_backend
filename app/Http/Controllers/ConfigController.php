<?php

namespace App\Http\Controllers;

use App\Settings\GeneralSettings;

class ConfigController extends Controller
{
    public function index()
    {
        return responder()->success([
            'generalSettings' => [
                'allowRegister' => app(GeneralSettings::class)->allow_register,
                'allowAuthWithSocial' => app(GeneralSettings::class)->allow_auth_with_social,
            ],
            'appName' => config('app.name'),
            'theme' => [
                'background' => '#ffffff',
                'surface' => '#f2f5f8',
                'primary' => '#00a54e',
                'secondary' => '#737373',
                'accent' => '#048ba8',
                'info' => '#5bc0de',
                'warning' => '#fcaf17',
                'success' => '#27ae60',
                'error' => '#e24343',
            ],
        ])->respond();
    }
}
