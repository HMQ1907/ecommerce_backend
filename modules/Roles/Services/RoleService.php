<?php

namespace Modules\Roles\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Roles\Models\Role;
use Modules\Roles\Repositories\PermissionRepository;
use Modules\Roles\Repositories\RoleRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RoleService extends BaseService
{
    protected $roleRepository;

    protected $permissionRepository;

    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function getRoles(array $params)
    {
        return QueryBuilder::for(Role::class)
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->where('display_name', 'LIKE', "%$q%");
                }),
            ])
            ->defaultSort('id')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getRole($id)
    {
        return $this->roleRepository->find($id);
    }

    public function createRole(array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->roleRepository->create([
                'name' => Str::slug($attrs['display_name']),
                'display_name' => $attrs['display_name'],
                'description' => $attrs['description'],
                'permissions' => $attrs['permission_ids'],
            ]);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editRole($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];
            if (isset($attrs['name'])) {
                $values['name'] = $attrs['name'];
            }
            if (isset($attrs['display_name'])) {
                $values['display_name'] = $attrs['display_name'];
            }
            if (isset($attrs['description'])) {
                $values['description'] = $attrs['description'];
            }
            if (isset($attrs['permission_ids'])) {
                $values['permissions'] = $attrs['permission_ids'];
            }

            $data = $this->roleRepository->update($values, $id);

            // TODO: move to observer
            foreach ($data->users as $user) {
                $user->syncPermissions($data->permissions);
            }

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteRole($id)
    {
        try {
            DB::beginTransaction();

            $this->roleRepository->deleteWhere([
                ['id', 'NOTIN', [Role::ADMIN, Role::USER]],
                ['id', '=', $id],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteRoles(array $ids)
    {
        try {
            DB::beginTransaction();

            $this->roleRepository->deleteWhere([
                ['id', 'NOTIN', [Role::ADMIN, Role::USER]],
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getModulePermissions()
    {
        return $this->permissionRepository->getAllPermissionsGroupedByModule();
    }

    public function getPermissions()
    {
        return $this->permissionRepository->getPermissions();
    }
}
