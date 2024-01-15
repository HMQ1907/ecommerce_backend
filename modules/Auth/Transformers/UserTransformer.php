<?php

namespace Modules\Auth\Transformers;

use App\Models\User;
use App\Transformers\BaseTransformer;

class UserTransformer extends BaseTransformer
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(User $model)
    {
        return [
            'id' => (int) $model->id,
            'current_branch_id' => $model->current_branch_id,
            'branch_id' => $model->branch_id,
            'name' => $model->name,
            'email' => $model->email,
            'account_type' => $model->account_type,
            'created_at' => $model->created_at,
            'avatar_url' => $model->avatar_url,
            'employee' => $model->employee,
            'roles' => $model->getRoleNames(),
            'role' => $model->getRoleNames()->first(),
            'scope' => $model->getDirectPermissions()->pluck('name'),
            'unread_count' => $model->unreadNotifications()->count(),
        ];
    }
}
