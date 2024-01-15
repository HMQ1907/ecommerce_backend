<?php

namespace Modules\Roles\Transformers;

use App\Transformers\BaseTransformer;

class ModulePermissionTransformer extends BaseTransformer
{
    /**
     * Transform the data.
     *
     * @param  array  $data
     * @return array
     */
    public function transform($data)
    {
        return [
            'module' => __($data['module']),
            'permissions' => transformation($data['permissions'], PermissionTransformer::class)->transform(),
        ];
    }
}
