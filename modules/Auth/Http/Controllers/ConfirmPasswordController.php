<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Confirm the given user's password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->validationErrorMessages());

        if ($validator->invalid()) {
            return responder()->error('password_incorrect', __('validation.current_password'))->respond(422);
        }

        $this->resetPasswordConfirmationTimeout($request);

        return responder()->success()->respond(Response::HTTP_NO_CONTENT);
    }

    /**
     * Reset the password confirmation timeout.
     *
     * @return void
     */
    protected function resetPasswordConfirmationTimeout(Request $request)
    {
        $redis = Redis::connection();

        $redis->set('auth.'.$request->bearerToken(), json_encode([
            'password_confirmed_at' => time(),
        ]));
    }

    /**
     * Get the password confirmation validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'password' => 'required|current_password:api',
        ];
    }
}
