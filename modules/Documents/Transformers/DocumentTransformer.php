<?php

namespace Modules\Documents\Transformers;

use App\Transformers\BaseTransformer;
use App\Transformers\MediaTransformer;
use Modules\Documents\Models\Document;

class DocumentTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'media',
    ];

    /**
     * Transform the Customer entity.
     *
     * @return array
     */
    public function transform(Document $model)
    {
        return [
            'id' => (int) $model->id,
            'branch_id' => $model->branch_id,
            'branch_name' => optional($model->branch)->name,
            'category_id' => $model->category_id,
            'type' => $model->type,
            'category_name' => optional($model->category)->name,
            'name' => $model->name,
            'content' => $model->content,
            'document_number' => $model->document_number,
            'issued_date' => $model->issued_date,
            'attachment_id' => optional($model->getFirstMedia('document_attachment'))->id,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }

    public function includeMedia(Document $model)
    {
        $media = $model->getFirstMedia('document_attachment');
        if ($media) {
            return $this->item($media, new MediaTransformer());
        }

        return $this->null();
    }
}
