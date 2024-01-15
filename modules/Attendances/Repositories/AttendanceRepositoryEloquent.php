<?php

namespace Modules\Attendances\Repositories;

use App\Repositories\BaseRepository;
use App\Settings\AttendanceSettings;
use DateTime;
use Illuminate\Support\Facades\DB;
use Modules\Attendances\Models\Attendance;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class AttendanceRepositoryEloquent extends BaseRepository implements AttendanceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Attendance::class;
    }

    /**
     * Boot up the repository, pushing criteria
     *
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getTotalPresentByDate($date = null)
    {
        if ($date == null) {
            $date = today();
        }

        return Attendance::query()
            ->whereDate('date', $date)
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->count();
    }

    public function getAttendanceBetweenDate($startDate, $endDate)
    {
        $attendances = DB::table('attendances as a')
            ->select(
                'a.employee_id',
                DB::raw("CONCAT(e.first_name, ' ', e.last_name) as full_name"),
                'e.code',
                'a.date',
                'a.clock_in',
                'a.clock_out',
            )
            ->rightJoin('employees as e', 'e.id', '=', 'a.employee_id')
            ->whereDate('a.date', '>=', $startDate)
            ->whereDate('a.date', '<=', $endDate)
            ->whereNull('a.deleted_at')
            ->orderBy('a.created_at')
            ->get();
        $groupedAttendances = [];
        $attendanceSettings = app(AttendanceSettings::class);
        foreach ($attendances as $attendance) {
            $employeeId = $attendance->employee_id;
            $date = $attendance->date;
            $clockIn = new DateTime($attendance->clock_in);
            if (empty($attendance->clock_out)) {
                $clockOut = new DateTime($attendanceSettings->work_time['end']);
            } else {
                $clockOut = new DateTime($attendance->clock_out);
            }
            $work_time_seconds = $clockOut->getTimestamp() - $clockIn->getTimestamp();

            if (!isset($groupedAttendances[$employeeId])) {
                $groupedAttendances[$employeeId] = [
                    'employee_id' => $employeeId,
                    'code' => $attendance->code,
                    'full_name' => $attendance->full_name,
                    'attendances' => [],
                ];
            }
            if (isset($groupedAttendances[$employeeId]['attendances'][$date])) {
                $groupedAttendances[$employeeId]['attendances'][$date]['clock_out'] = $clockOut->format('H:i:s');
                $groupedAttendances[$employeeId]['attendances'][$date]['work_time'] += $work_time_seconds;
            } else {
                $groupedAttendances[$employeeId]['attendances'][$date] = [
                    'date' => $date,
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => $clockOut->format('H:i:s'),
                    'work_time' => $work_time_seconds,
                ];
            }
        }
        $allEmployees = DB::table('employees')->get();
        foreach ($allEmployees as $employee) {
            $employeeId = $employee->id;

            if (!isset($groupedAttendances[$employeeId])) {
                $groupedAttendances[$employeeId] = [
                    'employee_id' => $employeeId,
                    'code' => $employee->code,
                    'full_name' => $employee->first_name.' '.$employee->last_name,
                    'attendances' => [],
                ];
            }
        }

        $result = array_values($groupedAttendances);
        foreach ($result as &$employee) {
            foreach ($employee['attendances'] as &$attendance) {
                $work_time_seconds = $attendance['work_time'];
                $hours = floor($work_time_seconds / 3600);
                $minutes = floor(($work_time_seconds % 3600) / 60);
                $seconds = $work_time_seconds % 60;
                $attendance['work_time'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                unset($attendance['work_time_seconds']);
            }
            $employee['attendances'] = array_values($employee['attendances']);
        }

        return $result;
    }
}
