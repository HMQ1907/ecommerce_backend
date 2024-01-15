<?php

namespace Modules\Departments\Models;

enum StatusDepartment: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;

    public function value(): int
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return match ($value) {
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
            default => throw new \InvalidArgumentException("Invalid status: {$value}"),
        };
    }
}
