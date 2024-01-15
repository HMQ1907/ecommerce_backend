<?php

namespace Modules\Roles\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Roles\Http\Requests\RoleCreateRequest;
use Modules\Roles\Http\Requests\RoleUpdateRequest;
use Modules\Roles\Services\RoleService;
use Modules\Roles\Transformers\ModulePermissionTransformer;
use Modules\Roles\Transformers\RoleTransformer;

class RolesController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;

        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only('store');
        $this->middleware('permission:roles.edit')->only('update');
        $this->middleware('permission:roles.delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->roleService->getRoles($request->all());

        return responder()->success($data, RoleTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleCreateRequest $request)
    {
        $data = $this->roleService->createRole($request->all());

        return responder()->success($data, RoleTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->roleService->getRole($id);

        return responder()->success($data, RoleTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        $data = $this->roleService->editRole($id, $request->all());

        return responder()->success($data, RoleTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->roleService->deleteRole($id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->roleService->deleteRoles($request->ids);

        return responder()->success()->respond();
    }

    public function getModulePermissions()
    {
        $permissions = $this->roleService->getModulePermissions();

        return responder()->success($permissions, ModulePermissionTransformer::class)->respond();
    }

    public function getPermissions()
    {
        $permissions = $this->roleService->getPermissions();

        return responder()->success($permissions)->respond();
    }
}
