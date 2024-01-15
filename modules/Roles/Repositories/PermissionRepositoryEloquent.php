<?php

namespace Modules\Roles\Repositories;

use Modules\Roles\Models\Permission;
use Modules\Roles\Models\Role;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class PermissionRepositoryEloquent extends BaseRepository implements PermissionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permission::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getAllPermissionsGroupedByModule()
    {
        return $this->findByModuleNotNull()->groupBy('module')->get()->map(function ($item) {
            $item->permissions = $this->findByField('module', $item->module);

            return $item->only(['module', 'permissions']);
        });
    }

    public function getPermissions()
    {
        return Permission::getPermissions()->pluck('name')->toArray();
    }

    public function syncPermissions(Role $role, array $permissions)
    {
        $role->syncPermissions($permissions);
    }

    private function findByModuleNotNull()
    {
        return Permission::query()->whereNotNull('module');
    }
}
