<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Employees\Services\EmployeeService;
use Modules\Users\Http\Requests\ChangePasswordRequest;
use Modules\Users\Http\Requests\UserUpdateSettingRequest;
use Modules\Users\Services\UserService;
use Modules\Users\Transformers\UserTransformer;

class ProfileController extends Controller
{
    protected $userService;

    protected $employeeService;

    public function __construct(UserService $userService, EmployeeService $employeeService)
    {
        $this->userService = $userService;
        $this->employeeService = $employeeService;
    }

    public function updateProfile(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $employee = $this->userService->editUser($user->id, $request->all());

            DB::commit();

            return responder()->success($employee, UserTransformer::class)->respond();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function updateUserSetting(UserUpdateSettingRequest $request)
    {
        $data = $this->userService->updateUserSetting($request->all());

        return responder()->success($data, UserTransformer::class)->respond();
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            if (!Hash::check($request->current_password, $user->password)) {
                return responder()->error('invalid_password', trans('errors.invalid_password'))->respond(400);
            }

            $this->userService->editUser($user->id, [
                'password' => $request->new_password,
            ]);

            $user->token()->revoke();
            $user->createToken($user->email)->accessToken;

            DB::commit();

            return responder()->success($user, UserTransformer::class)->respond();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
