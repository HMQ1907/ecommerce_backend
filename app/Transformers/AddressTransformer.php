<?php

namespace App\Transformers;

use Lecturize\Addresses\Models\Address;

class AddressTransformer extends BaseTransformer
{
    /**
     * Transform the Customer entity.
     *
     * @return array
     */
    public function transform(Address $model)
    {
        return [
            'id' => (int) $model->id,
            'street' => $model->street,
            'street_extra' => $model->street_extra,
            'state' => $model->state,
            'city' => $model->city,
            'post_code' => $model->post_code,
            'country_id' => $model->country_id,
            'full_address' => $this->getFullAddress($model),
            'notes' => $model->notes,
            'lat' => $model->lat,
            'lng' => $model->lng,
            'properties' => $model->properties,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }

    public function getFullAddress(Address $model)
    {
        $address = [];
        $address[] = $model->street ?? '';
        $address[] = $model->street_extra ?? '';
        $address[] = $model->state ?? '';
        $address[] = $model->city ?? '';

        if (count($address) > 0) {
            return implode(', ', array_filter($address));
        }

        return '';
    }
}
