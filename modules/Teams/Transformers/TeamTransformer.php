<?php

namespace Modules\Teams\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Teams\Models\Team;
use Modules\Users\Transformers\UserTransformer;

class TeamTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'owner',
    ];

    /**
     * Resources that can be included if requested.
     */
    protected array $availableIncludes = [
        'users',
    ];

    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Team $team)
    {
        return [
            'id' => $team->id,
            'owner_id' => $team->owner_id,
            'name' => $team->name,
            'total_members' => $team->users->count(),
        ];
    }

    public function includeOwner(Team $model)
    {
        if ($model->owner) {
            return $this->item($model->owner, new UserTransformer());
        }

        return $this->null();
    }

    public function includeUsers(Team $model)
    {
        return $this->collection($model->users, new UserTransformer());
    }
}
