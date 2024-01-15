<?php

namespace App\Transformers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaTransformer extends BaseTransformer
{
    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(Media $attachment)
    {
        return [
            'id' => $attachment->id,
            'name' => $attachment->name,
            'filename' => $attachment->file_name,
            'extension' => $attachment->extension,
            'mime_type' => $attachment->mime_type,
            'size' => $attachment->size,
            'human_size' => $this->humanFileSize($attachment->size),
            'url' => $attachment->getUrl(),
        ];
    }

    protected function humanFileSize($size, $precision = 2)
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }

        return round($size, $precision).$units[$i];
    }
}
