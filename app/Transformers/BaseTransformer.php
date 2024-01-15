<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use Modules\Users\Transformers\UserTransformer;

class BaseTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested.
     */
    protected array $availableIncludes = [
        'creator',
        'editor',
    ];

    public function includeCreator(Model $model)
    {
        if ($model->creator) {
            return $this->item($model->creator, new UserTransformer());
        }

        return $this->null();
    }

    public function includeEditor(Model $model)
    {
        if ($model->editor) {
            return $this->item($model->editor, new UserTransformer());
        }

        return $this->null();
    }
}
