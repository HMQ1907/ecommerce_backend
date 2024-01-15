<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EmployeeContractFileTransformer extends BaseTransformer
{
    /**
     * Transform the entity.
     *
     * @return array
     */
    public function transform(Media $model)
    {
        return [
            'id' => $model->id,
            'filename' => $model->file_name,
            'created_at' => $model->created_at->format('Y-m-d'),
            'extension' => $model->extension,
            'mime_type' => $model->mime_type,
            'size' => $model->size,
            'url' => $model->getUrl(),
        ];
    }
}
