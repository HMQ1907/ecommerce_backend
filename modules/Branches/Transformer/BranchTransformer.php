<?php

namespace Modules\Branches\Transformer;

use App\Transformers\BaseTransformer;
use Modules\Branches\Models\Branch;

class BranchTransformer extends BaseTransformer
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Branch $model)
    {
        return [
            'id' => (int) $model->id,
            'name' => $model->name,
        ];
    }
}
