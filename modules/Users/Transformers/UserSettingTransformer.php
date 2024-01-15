<?php

namespace Modules\Users\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Users\Models\UserSetting;

class UserSettingTransformer extends BaseTransformer
{
    /**
     * Transform the Customer entity.
     *
     * @return array
     */
    public function transform(UserSetting $model)
    {
        return [
            'allowed_notification' => $model->allowed_notification,
            'allowed_location' => $model->allowed_location,
            'latest_platform' => $model->latest_platform,
            'platform_version' => $model->platform_version,
        ];
    }
}
