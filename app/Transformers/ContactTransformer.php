<?php

namespace App\Transformers;

use Lecturize\Addresses\Models\Contact;

class ContactTransformer extends BaseTransformer
{
    /**
     * Transform the Customer entity.
     *
     * @return array
     */
    public function transform(Contact $model)
    {
        $bankInformation = json_decode($model->extra, true);

        return [
            'id' => (int) $model->id,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'full_name' => $model->full_name,
            'email' => $model->email,
            'phone' => $model->phone,
            'position' => $model->position,
            'bank_name' => data_get($bankInformation, 'bank_name'),
            'account_name' => data_get($bankInformation, 'account_name'),
            'account_number' => data_get($bankInformation, 'account_number'),
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}
