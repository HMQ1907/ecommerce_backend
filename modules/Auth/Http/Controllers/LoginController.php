<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Exceptions\AccountInactiveException;
use Modules\Auth\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Login user and create token
     *
     * @return JsonResponse [string] access_token
     */
    protected function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->email)->first();
        if (!$user) {
            return responder()->error('auth.failed', trans('auth.failed'))
                ->respond(Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($request->password, $user->password)) {
            return responder()->error('auth.failed', trans('auth.failed'))
                ->respond(Response::HTTP_UNAUTHORIZED);
        }

        if ($user->status === User::STATUS_INACTIVE) {
            return throw new AccountInactiveException();
        }

        $tokenResult = $user->createToken($user->email);
        $token = $tokenResult->token;
        $token->save();

        activity('login')
            ->causedBy($user)
            ->log('user_logged_in');

        return responder()->success([
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at->timestamp,
            'scope' => $user->getDirectPermissions()->pluck('name'),
            'roles' => $user->getRoleNames(),
            'user' => $user,
        ])->respond();
    }

    public function logout()
    {
        Auth::user()->token()->revoke();
    }
}
