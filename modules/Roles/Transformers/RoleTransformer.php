<?php

namespace Modules\Roles\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Roles\Models\Role;

class RoleTransformer extends BaseTransformer
{
    /**
     * Resources that can be included if requested.
     */
    protected array $availableIncludes = [
        'permissions',
    ];

    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => (int) $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
            'permission_ids' => $role->permissions->pluck('id'),
            'is_deletable' => $role->isDeletable(),
        ];
    }

    public function includePermissions(Role $model)
    {
        return $this->collection($model->permissions, new PermissionTransformer());
    }
}
