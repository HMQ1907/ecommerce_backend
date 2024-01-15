<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Roles\Models\Role;
use Modules\Users\Http\Requests\UserChangePasswordRequest;
use Modules\Users\Http\Requests\UserCreateRequest;
use Modules\Users\Http\Requests\UserUpdatePermissionsRequest;
use Modules\Users\Http\Requests\UserUpdateRequest;
use Modules\Users\Http\Requests\UserUpdateRolesRequest;
use Modules\Users\Services\UserService;
use Modules\Users\Transformers\UserTransformer;

class UsersController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('permission:roles.edit_user_roles')->only('updateRoles');
        $this->middleware('role:'.Role::ADMIN)->only('toggleStatus');
    }

    public function index(Request $request)
    {
        $data = $this->userService->getUsers($request->all());

        return responder()->success($data, UserTransformer::class)->respond();
    }

    public function store(UserCreateRequest $request)
    {
        $data = $this->userService->createUser($request->all());

        return responder()->success($data, UserTransformer::class)->respond();
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $data = $this->userService->editUser($id, $request->all());

        return responder()->success($data, UserTransformer::class)->respond();
    }

    public function changePassword(UserChangePasswordRequest $request, $id)
    {
        $data = $this->userService->editUser($id, $request->all());

        return responder()->success($data, UserTransformer::class)->respond();
    }

    public function updateRoles(UserUpdateRolesRequest $request, $id)
    {
        $data = $this->userService->syncUserRoles($id, $request->all());

        return responder()->success($data, UserTransformer::class)->respond();
    }

    public function getPermissions($id)
    {
        $data = $this->userService->getPermissions($id);

        return responder()->success($data)->respond();
    }

    public function updatePermissions(UserUpdatePermissionsRequest $request, $id)
    {
        $data = $this->userService->syncUserPermissions($id, $request->all());

        return responder()->success($data)->respond();
    }

    public function toggleStatus($id)
    {
        if ($id != auth()->id()) {
            $this->userService->toggleStatus($id);
        }

        return responder()->success()->respond();
    }

    public function assignBranch(Request $request)
    {
        $user = auth()->user();
        $user->branch_id = $request->branch_id;
        $user->save();

        return responder()->success()->respond();
    }
}
