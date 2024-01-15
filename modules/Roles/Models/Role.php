<?php

namespace Modules\Roles\Models;

use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    const ADMIN = 'admin';

    const USER = 'user';

    const SALES = 'sales';

    const CHECKER = 'checker';

    const DRIVER = 'driver';

    const ACCOUNTANT = 'accountant';

    const COORDINATOR = 'coordinator';

    const CUSTOMER = 'customer';

    const EMPLOYEE = 'employee';

    public function isDeletable()
    {
        return !in_array($this->name, [self::ADMIN, self::USER, self::SALES, self::CHECKER, self::DRIVER, self::ACCOUNTANT, self::COORDINATOR]);
    }
}
