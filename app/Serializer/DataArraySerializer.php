<?php

namespace App\Serializer;

use League\Fractal\Serializer\ArraySerializer;

class DataArraySerializer extends ArraySerializer
{
    /**
     * Serialize a collection.
     */
    public function collection(?string $resourceKey, array $data): array
    {
        return $data;
    }

    /**
     * Serialize an item.
     */
    public function item(?string $resourceKey, array $data): array
    {
        return $data;
    }

    /**
     * Serialize null resource.
     */
    public function null(): ?array
    {
        return [];
    }
}
