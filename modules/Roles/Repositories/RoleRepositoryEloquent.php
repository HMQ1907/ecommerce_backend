<?php

namespace Modules\Roles\Repositories;

use Modules\Roles\Models\Role;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class RoleRepositoryEloquent extends BaseRepository implements RoleRepository
{
    protected $permissionRepository;

    public function __construct(PermissionRepositoryEloquent $permissionRepository)
    {
        parent::__construct(app());

        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Role::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function create(array $attributes)
    {
        $role = parent::create($attributes);

        $this->permissionRepository->syncPermissions($role, $attributes['permissions']);

        return $role;
    }

    public function update(array $attributes, $id)
    {
        $role = parent::update($attributes, $id);

        $this->permissionRepository->syncPermissions($role, $attributes['permissions']);

        return $role;
    }
}
