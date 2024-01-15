<?php

namespace Modules\Employees\Transformers;

use League\Fractal\TransformerAbstract;

class AttachmentEmployeeTransformer extends TransformerAbstract
{
    /**
     * Transform the Customer entity.
     *
     * @return array
     */
    public function transform($attachment)
    {
        return [
            'id' => $attachment->id,
            'disk' => $attachment->disk,
            'directory' => $attachment->directory,
            'filename' => $attachment->filename,
            'extension' => $attachment->extension,
            'size' => $attachment->size,
            'variant_name' => $attachment->variant_name,
            'original_media_id' => $attachment->original_media_id,
            'created_at' => $attachment->created_at,
            'url_document' => $attachment->getUrl(),
        ];
    }
}
