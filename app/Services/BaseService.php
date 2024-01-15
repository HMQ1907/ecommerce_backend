<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Roles\Models\Role;

class BaseService
{
    const TAX_0_PERCENT = 1300000;

    const TAX_5_PERCENT = 3700000;

    const TAX_10_PERCENT = 10000000;

    const TAX_15_PERCENT = 10000000;

    const TAX_20_PERCENT = 40000000;

    public function isAdmin()
    {
        return auth()->user()->hasRole(Role::ADMIN);
    }

    public function getEmployeeId()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $request = request()->all();

        return data_get($request, 'employee_id', $employee->id);
    }

    protected function calculateQuantity($quantity, $actualQty)
    {
        $currentQty = $quantity ?? 0;

        if ($actualQty > $currentQty) {
            $adjustmentType = 'increase';
        } elseif ($actualQty < $currentQty) {
            $adjustmentType = 'decrease';
        } else {
            $adjustmentType = 'normal';
        }

        return [
            'current_quantity' => $currentQty,
            'quantity' => abs($actualQty - $currentQty),
            'adjustment_type' => $adjustmentType,
        ];
    }

    protected function paginate(Collection $collect, $limit, $page)
    {
        $offset = ($page * $limit) - $limit;
        $itemsForCurrentPage = $collect->slice($offset, $limit)->all();

        return new LengthAwarePaginator($itemsForCurrentPage, count($collect), $limit, $page);
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

    protected function getWorkingDays($from, $to)
    {
        $workingDays = [1, 2, 3, 4, 5, 6];

        $from = new \DateTime($from);
        $to = new \DateTime($to);
        $to->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($from, $interval, $to);
        $days = 0;

        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) {
                continue;
            }

            $days++;
        }

        return $days;
    }

    protected function calculateDistance($lat, $lng, $currentLat, $currentLng)
    {
        $earthRadius = 6371; // Earth's diameter in kilometers

        $dLat = deg2rad($currentLat - $lat);
        $dLng = deg2rad($currentLng - $lng);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat)) * cos(deg2rad($currentLat)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }

    public function rangeDateInMonth($startDate, $endDate)
    {
        $monthsArray = [];

        while ($startDate <= $endDate) {
            $monthStart = $startDate->copy()->startOfMonth();
            $monthEnd = $startDate->copy()->endOfMonth();

            $monthsArray[] = [
                'start' => $monthStart->format('Y-m-d'),
                'end' => $monthEnd->format('Y-m-d'),
            ];

            $startDate->addMonth();
        }

        return $monthsArray;
    }

    protected function calculateSocialSecurity($basicSalary)
    {
        if ($basicSalary >= 4500000) {
            $socialSecurity = 4500000 * 0.055;
        } else {
            $socialSecurity = $basicSalary * 0.055;
        }

        return $socialSecurity;
    }

    public function calcPersonalIncomeTax($totalSalary)
    {
        if ($totalSalary <= 1300000) {
            return 0;
        } elseif ($totalSalary > 1300000 && $totalSalary <= 5000000) {
            $tax5Percent = $this->tax5Percent($totalSalary);

            return $tax5Percent * 0.05;
        } elseif ($totalSalary > 5000000 && $totalSalary <= 15000000) {
            $tax10Percent = $this->tax10Percent($totalSalary);

            return self::TAX_5_PERCENT * 0.05 + $tax10Percent * 0.1;
        } elseif ($totalSalary > 15000000 && $totalSalary <= 25000000) {
            $tax15Percent = $this->tax15Percent($totalSalary);

            return self::TAX_5_PERCENT * 0.05 + self::TAX_10_PERCENT * 0.1 + $tax15Percent * 0.15;
        } elseif ($totalSalary > 25000000 && $totalSalary <= 65000000) {
            $tax20Percent = $this->tax20Percent($totalSalary);

            return self::TAX_5_PERCENT * 0.05 + self::TAX_10_PERCENT * 0.1 + self::TAX_15_PERCENT * 0.15 + $tax20Percent * 0.2;
        } else {
            $tax25Percent = $this->tax25Percent($totalSalary);

            return self::TAX_5_PERCENT * 0.05 + self::TAX_10_PERCENT * 0.1 + self::TAX_15_PERCENT * 0.15 + self::TAX_20_PERCENT * 0.2 + $tax25Percent * 0.25;
        }
    }

    public function tax5Percent($totalSalary)
    {
        if ($totalSalary <= 1300000) {
            return 0;
        } elseif ($totalSalary <= 5000000) {
            return $totalSalary - 1300000;
        } else {
            return 5000000 - 1300000;
        }
    }

    public function tax10Percent($totalSalary)
    {
        if ($totalSalary <= 5000000) {
            return 0;
        } elseif ($totalSalary <= 15000000) {
            return $totalSalary - 5000000;
        } else {
            return 15000000 - 5000000;
        }
    }

    public function tax15Percent($totalSalary)
    {
        if ($totalSalary <= 15000000) {
            return 0;
        } elseif ($totalSalary <= 25000000) {
            return $totalSalary - 15000000;
        } else {
            return 25000000 - 15000000;
        }
    }

    public function tax20Percent($totalSalary)
    {
        if ($totalSalary <= 25000000) {
            return 0;
        } elseif ($totalSalary <= 65000000) {
            return $totalSalary - 25000000;
        } else {
            return 65000000 - 25000000;
        }
    }

    public function tax25Percent($totalSalary)
    {
        return $totalSalary - self::TAX_0_PERCENT - self::TAX_5_PERCENT - self::TAX_10_PERCENT - self::TAX_15_PERCENT - self::TAX_20_PERCENT;
    }
}
