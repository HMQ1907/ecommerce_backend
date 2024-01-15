<?php

namespace Modules\Roles\Repositories;

use Modules\Roles\Models\Role;
use Prettus\Repository\Contracts\RepositoryInterface;

interface PermissionRepository extends RepositoryInterface
{
    public function getAllPermissionsGroupedByModule();

    public function getPermissions();

    public function syncPermissions(Role $role, array $permissions);
}
