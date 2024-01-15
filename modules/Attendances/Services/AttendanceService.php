<?php

namespace Modules\Attendances\Services;

use App\Services\BaseService;
use App\Settings\AttendanceSettings;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Modules\Attendances\Events\CheckInCreated;
use Modules\Attendances\Exceptions\UnCheckedInException;
use Modules\Attendances\Exceptions\UnCheckedOutException;
use Modules\Attendances\Exports\AttendanceExport;
use Modules\Attendances\Models\Attendance;
use Modules\Attendances\Repositories\AttendanceRepository;
use Modules\Employees\Models\Employee;
use Modules\Employees\Repositories\EmployeeRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttendanceService extends BaseService
{
    protected $attendanceRepository;

    protected $employeeRepository;

    public function __construct(AttendanceRepository $attendanceRepository, EmployeeRepository $employeeRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function getAttendances(array $params)
    {
        $fromDate = data_get($params, 'filter.from_date', now());
        $toDate = data_get($params, 'filter.to_date', now());
        $period = CarbonPeriod::create($fromDate, $toDate);

        $items = [];

        foreach ($period as $date) {
            $items[] = [
                'date' => $date->format('Y-m-d'),
                'attendances' => QueryBuilder::for(Attendance::ownership())
                    ->with(['employee'])
                    ->whereDate('date', $date)
                    ->allowedFilters([
                        AllowedFilter::callback('from_date', function (Builder $query, $fromDate) use ($params) {
                            $toDate = data_get($params, 'filter.to_date', now());

                            $query->where(function ($query) use ($fromDate, $toDate) {
                                $query->whereBetween('date', [$fromDate, $toDate]);
                            });
                        }),
                        AllowedFilter::callback('to_date', function (Builder $query, $toDate) use ($params) {
                            $fromDate = data_get($params, 'filter.from_date', now());

                            $query->where(function ($query) use ($fromDate, $toDate) {
                                $query->whereBetween('date', [$fromDate, $toDate]);
                            });
                        }),
                        AllowedFilter::exact('date'),
                    ])
                    ->defaultSorts('-date')
                    ->paginate(data_get($params, 'limit', config('repository.pagination.limit'))),
            ];
        }

        return $items;
    }

    public function getEmployeeAttendances(array $params)
    {
        return QueryBuilder::for(Employee::allData())
            ->with([
                'attendances' => function ($query) use ($params) {
                    $date = data_get($params, 'filter.date');
                    if (!empty($date)) {
                        if (data_get($params, 'filter.type') == 'month') {
                            $query->whereMonth('date', Carbon::parse($date)->month)->get();
                        } else {
                            $query->whereDate('date', $date)->get();
                        }
                    }
                },
                'attendances.employee',
                'attendances.media',
            ])
            ->allowedFilters([
                AllowedFilter::exact('employee_id', 'id'),
            ])
            ->allowedSorts([
                'created_at',
                'clock_in',
                'clock_out',
            ])
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getAttendance($id)
    {
        return $this->attendanceRepository->find($id);
    }

    public function getAttendanceByEmployee($employeeId, array $params)
    {
        return Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', data_get($params, 'date'))
            ->latest()
            ->firstOrFail();
    }

    public function createAttendance(array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->attendanceRepository->create([
                'employee_id' => $attrs['employee_id'],
                'date' => $attrs['date'],
                'clock_in' => $attrs['clock_in'],
                'clock_out' => $attrs['clock_out'],
            ]);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editAttendance($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $attendance = $this->attendanceRepository->find($id);

            $values = [];
            if (isset($attrs['employee_id'])) {
                $values['employee_id'] = $attendance->employee_id;
            }
            if (isset($attrs['date'])) {
                $values['date'] = $attrs['date'];
            }
            if (isset($attrs['clock_in'])) {
                $values['clock_in'] = $attrs['clock_in'];
            }
            if (isset($attrs['clock_out'])) {
                $values['clock_out'] = $attrs['clock_out'];
            }

            $data = $this->attendanceRepository->update($values, $id);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function checkIn(array $attrs, $image = null)
    {
        try {
            DB::beginTransaction();

            $latitude = data_get($attrs, 'latitude');
            $longitude = data_get($attrs, 'longitude');

            $attendanceSettings = app(AttendanceSettings::class);

            if (!empty($latitude) && !empty($longitude)) {
                $lat = $attendanceSettings->lat;
                $lng = $attendanceSettings->lng;
                $radius = $attendanceSettings->radius;

                $distance = $this->calculateDistance($lat, $lng, $latitude, $longitude);

                if ($radius < $distance) {
                    throw new \Exception(__('attendances::common.errorCheckIn'));
                }
            }

            $ip = data_get($attrs, 'ip');

            if (!empty($ip)) {
                $ips = explode(',', $attendanceSettings->ips);

                if (!in_array($ip, $ips)) {
                    throw new \Exception(__('attendances::common.errorWifiIp'));
                }
            }

            $employee = auth()->user()->employee;

            $attendanceToday = $this->hasAttendance($employee->id, today());

            if (!empty($attendanceToday)) {
                throw new UnCheckedOutException();
            }

            $clockIn = now();
            $workStart = Carbon::createFromFormat('H:i', $attendanceSettings->work_time['start']);

            $data = $this->attendanceRepository->create([
                'employee_id' => $employee->id,
                'date' => $clockIn->format('Y-m-d'),
                'is_late' => $clockIn->isAfter($workStart),
                'clock_in' => $clockIn->format('H:i:s'),
                'clock_in_latitude' => $latitude,
                'clock_in_longitude' => $longitude,
            ]);

            if (isset($image)) {
                $data->addMedia($image)->toMediaCollection('check_in_image');
            }

            event(new CheckInCreated($data));

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function checkOut($image = null)
    {
        try {
            DB::beginTransaction();

            $employee = auth()->user()->employee;

            $attendanceToday = $this->hasAttendance($employee->id, today());

            if (empty($attendanceToday)) {
                throw new UnCheckedInException();
            }

            $attendanceSettings = app(AttendanceSettings::class);

            $clockOut = now();
            $workEnd = Carbon::createFromFormat('H:i', $attendanceSettings->work_time['end']);

            $data = $this->attendanceRepository->update([
                'is_early' => $clockOut->isBefore($workEnd),
                'clock_out' => $clockOut->format('H:i:s'),
            ], $attendanceToday->id);

            if (isset($image)) {
                $data->addMedia($image)->toMediaCollection('check_out_image');
            }

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteAttendance($id)
    {
        try {
            DB::beginTransaction();

            $this->attendanceRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteAttendances(array $ids)
    {
        try {
            DB::beginTransaction();

            $this->attendanceRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getAttendanceInRange($employeeId, array $attrs)
    {
        $data = [];

        $format = 'Y-m-d';
        $to = Carbon::parse($attrs['date']);
        $from = $to->clone()->startOf('month');
        $group = null;

        switch ($attrs['type']) {
            case 'year':
                $format = 'Y-m';
                for ($i = 1; $i <= 12; $i++) {
                    $data[$to->clone()->setMonth($i)->format($format)] = 0;
                }
                $from = $to->clone()->startOf('year');
                $group = 'month';
                break;
            case 'month':
                for ($i = 1; $i <= $to->daysInMonth; $i++) {
                    $data[$to->clone()->setDay($i)->format($format)] = 0;
                }
                break;
            case 'week':
                $period = CarbonPeriod::create($to->clone()->subDays(7), $to);
                foreach ($period as $item) {
                    $data[$item->format($format)] = 0;
                }
                break;
        }

        $attendances = $this->totalHours($employeeId, $from, $to, $group);

        return $this->formatChart($data, $attendances, $format);
    }

    private function formatChart(&$data, $attendances, $format, $round = 2)
    {
        foreach ($attendances as $item) {
            $clockOut = Carbon::parse($item->clock_out);
            $clockIn = Carbon::parse($item->clock_in);

            $duration = round($clockOut->diffInMinutes($clockIn) / 60, $round);

            $data[Date::parse($item->date)->format($format)] = $duration;
        }

        return $data;
    }

    private function totalHours($employeeId, $from, $to, $group = null)
    {
        $query = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$from, $to]);

        if (!empty($group)) {
            $query->groupBy(DB::raw("$group(date)"));
        }

        return $query->get();
    }

    private function hasAttendance($employeeId, $date)
    {
        return Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->latest()
            ->first();
    }

    public function getEmployeeAttendanceCount($params)
    {
        $month = Carbon::parse(data_get($params, 'filter.month'))->month;
        $year = Carbon::now()->year;

        return Attendance::query()
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->select(
                'employees.first_name',
                'employees.last_name',
                'employees.avatar',
                'employees.id',
                DB::raw('(SELECT GROUP_CONCAT(name) FROM roles
                INNER JOIN model_has_roles
                ON roles.id = model_has_roles.role_id WHERE model_has_roles.model_id = users.id
                AND model_has_roles.model_type = "App\\\Models\\\User") as roles'),
                DB::raw('COUNT(DISTINCT DATE(attendances.date)) as attendance_count')
            )
            ->whereMonth('attendances.date', $month)
            ->whereYear('attendances.date', $year)
            ->groupBy('employees.id')
            ->orderBy('attendance_count', 'desc')
            ->limit(10)
            ->get();
    }

    public function getTotalDelayTime(array $params)
    {
        $rangeMonth = $this->rangeDateInMonth(Carbon::parse($params['start_date']), Carbon::parse($params['end_date']));
        $totalDelayMinutes = [];
        $standardTime = app(AttendanceSettings::class);
        $startWork = Carbon::parse($standardTime->work_time['start']);
        $totalLates = [];
        foreach ($rangeMonth as $month) {
            $totalDelayMinute = 0;
            $lateCount = 0;
            $attendances = Attendance::query()
                ->select('clock_in')
                ->whereDate('date', '>=', $month['start'])
                ->whereDate('date', '<=', $month['end'])
                ->get();

            foreach ($attendances as $attendance) {
                $clockIn = Carbon::parse($attendance->clock_in);

                if ($startWork->lessThan($clockIn)) {
                    $delay = $clockIn->diffInMinutes($startWork);
                    $totalDelayMinute += $delay;
                }
                if ($startWork->lessThan($clockIn)) {
                    $lateCount++;
                }
            }
            $totalDelayMinutes[] = $totalDelayMinute;
            $totalLates[] = $lateCount;
        }

        $data = [
            'totalLates' => $totalLates,
            'totalTimeDelays' => $totalDelayMinutes,
        ];

        return $data;
    }

    public function getTotalEarly(array $params)
    {
        $rangeMonths = $this->rangeDateInMonth(Carbon::parse($params['start_date']), Carbon::parse($params['end_date']));
        $standardTime = app(AttendanceSettings::class);
        $endWork = Carbon::parse($standardTime->work_time['end']);
        foreach ($rangeMonths as $month) {
            $totalEarlyMinute = 0;
            $earlyCount = 0;
            $attendances = Attendance::query()
                ->select('clock_out')
                ->whereDate('date', '>=', $month['start'])
                ->whereDate('date', '<=', $month['end'])
                ->get();

            foreach ($attendances as $attendance) {
                $clockOut = Carbon::parse($attendance->clock_out);

                if ($clockOut->lessThan($endWork)) {
                    $early = $endWork->diffInMinutes($clockOut);
                    $totalEarlyMinute += $early;
                }
                if ($clockOut->lessThan($endWork)) {
                    $earlyCount++;
                }
            }
            $totalEarlyMinutes[] = $totalEarlyMinute;
            $totalEarly[] = $earlyCount;
        }

        $data = [
            'totalEarly' => $totalEarly,
            'totalTimeEarly' => $totalEarlyMinutes,
        ];

        return $data;
    }

    public function getTotalWorkTime(array $attrs)
    {
        $rangeMonth = $this->rangeDateInMonth(Carbon::parse($attrs['start_date']), Carbon::parse($attrs['end_date']));
        $employeeWorking = [];
        $totalWorkingHoursInMonths = [];
        foreach ($rangeMonth as $month) {
            $attendances = Attendance::query()
                ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))) as total')
                ->whereDate('date', '>=', $month['start'])
                ->whereDate('date', '<=', $month['end'])
                ->get();
            $employeeWorking[] = $attendances->sum('total') / 60;
            $totalWorkingHoursInMonths[] = 0;
        }

        return [
            'totalWorking' => $totalWorkingHoursInMonths,
            'totalEmployeeWorking' => $employeeWorking,
        ];
    }

    public function exportAttendance(array $params)
    {
        $filter = $params['filter'] ?? [];
        if (!empty($filter['date'])) {
            $startDate = Carbon::parse($filter['date'])->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::parse($filter['date'])->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = null;
            $endDate = null;
        }
        $data = $this->attendanceRepository->getAttendanceBetweenDate($startDate, $endDate);

        return (new AttendanceExport($data, $startDate, $endDate))->download('attendance.xlsx');
    }
}
