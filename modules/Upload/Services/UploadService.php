<?php

namespace Modules\Upload\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadService extends BaseService
{
    public function upload($file)
    {
        if (is_array($file)) {
            $data = [];

            foreach ($file as $item) {
                $data[] = $this->store($item);
            }

            return collect($data)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'url' => $item->getUrl(),
                ];
            });
        }

        $data = $this->store($file);

        return [
            'id' => $data->id,
            'url' => $data->getUrl(),
        ];
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            Media::query()->find($id)->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function store($file)
    {
        return auth()->user()->addMedia($file)->toMediaCollection();
    }
}
