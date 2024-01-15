<?php

namespace Modules\Users\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'type' => $model->type,
            'notifiable_type' => $model->notifiable_type,
            'notifiable_id' => $model->notifiable_id,
            'data' => [
                ...$model->data,
                'action' => (object) $model->data['action'],
            ],
            'read_at' => $model->read_at,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}
