<?php

namespace Modules\Roles\Models;

use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    const ADMIN = 'admin';

    const CUSTOMER = 'customer';

    public function isDeletable()
    {
        return !in_array($this->name, [self::ADMIN, self::CUSTOMER]);
    }
}
