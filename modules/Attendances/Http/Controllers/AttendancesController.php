<?php

namespace Modules\Attendances\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Attendances\Http\Requests\AttendanceRequest;
use Modules\Attendances\Models\Attendance;
use Modules\Attendances\Services\AttendanceService;
use Modules\Attendances\Transformers\AttendanceByDateTransformer;
use Modules\Attendances\Transformers\AttendanceTransformer;
use Modules\Attendances\Transformers\EmployeeAttendanceTransformer;

class AttendancesController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
        $this->authorizeResource(Attendance::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->attendanceService->getAttendances($request->all());

        return responder()->success($data, AttendanceByDateTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AttendanceRequest $request)
    {
        $data = $this->attendanceService->createAttendance($request->all());

        return responder()->success($data, AttendanceTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Attendance $attendance)
    {
        $data = $this->attendanceService->getAttendance($attendance->id);

        return responder()->success($data, AttendanceTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AttendanceRequest $request, Attendance $attendance)
    {
        $data = $this->attendanceService->editAttendance($attendance->id, $request->all());

        return responder()->success($data, AttendanceTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Attendance $attendance)
    {
        $this->attendanceService->deleteAttendance($attendance->id);

        return responder()->success()->respond();
    }

    public function bulkDestroy(Request $request)
    {
        $this->attendanceService->deleteAttendances($request->ids);

        return responder()->success()->respond();
    }

    public function checkIn(Request $request)
    {
        $data = $this->attendanceService->checkIn($request->all(), $request->file('image'));

        return responder()->success($data, AttendanceTransformer::class)->respond();
    }

    public function checkOut(Request $request)
    {
        $data = $this->attendanceService->checkOut($request->file('image'));

        return responder()->success($data, AttendanceTransformer::class)->respond();
    }

    public function getEmployeeAttendances(Request $request)
    {
        $data = $this->attendanceService->getEmployeeAttendances($request->all());

        return responder()->success($data, EmployeeAttendanceTransformer::class)->respond();
    }

    public function getEmployeeAttendance($id, Request $request)
    {
        $data = $this->attendanceService->getAttendanceByEmployee($id, $request->all());
        $results = $this->attendanceService->getAttendanceInRange($id, $request->all());

        return responder()->success([
            'chart' => $results,
            'attendance' => transformation($data, AttendanceTransformer::class)->transform(),
        ])->respond();
    }

    public function getEmployeeAttendanceCount(Request $request)
    {
        $totalWorkTime = $this->attendanceService->getEmployeeAttendanceCount($request->all());

        return responder()->success($totalWorkTime)->respond();
    }

    public function getTotalDelayTime(Request $request)
    {
        $totalDelayTime = $this->attendanceService->getTotalDelayTime($request->all());

        return responder()->success($totalDelayTime)->respond();
    }

    public function getTotalEarlyTime(Request $request)
    {
        $totalEarlyTime = $this->attendanceService->getTotalEarly($request->all());

        return responder()->success($totalEarlyTime)->respond();
    }

    public function getTotalWorkTime(Request $request)
    {
        $totalWorkTime = $this->attendanceService->getTotalWorkTime($request->all());

        return responder()->success($totalWorkTime)->respond();
    }

    public function exportAttendance(Request $request)
    {
        return $this->attendanceService->exportAttendance($request->all());
    }
}
