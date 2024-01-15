<?php

namespace Modules\Users\Transformers;

use App\Models\User;
use App\Transformers\BaseTransformer;
use Modules\Roles\Transformers\RoleTransformer;

class UserTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'setting',
    ];

    /**
     * Resources that can be included if requested.
     */
    protected array $availableIncludes = [
        'roles',
    ];

    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(User $model)
    {
        return [
            'id' => (int) $model->id,
            'branch_id' => $model->branch_id,
            'branch_name' => optional($model->branch)->name,
            'name' => $model->name,
            'email' => $model->email,
            'account_type' => $model->account_type,
            'status' => $model->status,
            'created_at' => $model->created_at,
            'avatar_url' => $model->avatar_url,
            'roles' => $model->roles,
            'role_ids' => $model->roles->pluck('id'),
            'employee_id' => $model->employee->id ?? null,
        ];
    }

    public function includeSetting(User $model)
    {
        if (!empty($model->setting)) {
            return $this->item($model->setting, new UserSettingTransformer());
        }

        return $this->null();
    }

    public function includeRoles(User $model)
    {
        return $this->collection($model->roles, new RoleTransformer());
    }
}
