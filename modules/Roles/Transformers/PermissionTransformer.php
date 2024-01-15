<?php

namespace Modules\Roles\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Roles\Models\Permission;

class PermissionTransformer extends BaseTransformer
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Permission $permission)
    {
        return [
            'id' => (int) $permission->id,
            'name' => __($permission->name),
            'description' => __($permission->description),
        ];
    }
}
