<?php

namespace Modules\Comments\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Comments\Models\Comment;
use Modules\Employees\Transformers\EmployeeTransformer;

class CommentTransformer extends TransformerAbstract
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'commenter',
    ];

    /**
     * Transform the Comment entity.
     *
     * @return array
     */
    public function transform(Comment $model)
    {
        return [
            'id' => (int) $model->id,
            'message' => $model->comment,
            'created_at' => $model->created_at,
        ];
    }

    public function includeCommenter(Comment $model)
    {
        if ($model->commenter) {
            return $this->item($model->commenter->employee, new EmployeeTransformer());
        }

        return $this->null();
    }
}
