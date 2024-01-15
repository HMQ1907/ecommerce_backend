<?php

namespace Modules\Attendances\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface AttendanceRepository extends RepositoryInterface
{
    public function getTotalPresentByDate($date = null);

    public function getAttendanceBetweenDate($startDate, $endDate);
}
