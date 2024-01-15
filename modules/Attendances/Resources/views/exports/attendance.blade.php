<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            margin: 0;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .table td, .table th {
            border: 1px solid #000 !important;
            padding: 8px;
            font-family: Arial, Helvetica, sans-serif;
            vertical-align: top !important;
            text-align: left;
        }

    </style>

</head>
<body>
<div>
    <table width="100%">
        <thead>
        <tr>
            <th colspan="7" align="center" style="font-weight: bold;">MISA</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="7" align="center" style="font-weight: bold;">BÁO CÁO CHẤM CÔNG</th>
        </tr>
        <tr>
            <th colspan="7" align="center" style="font-weight: bold;">Từ
                ngày {{ date('d-m-Y', strtotime($startDate)) }}
                đến ngày {{ date('d-m-Y', strtotime($endDate)) }}</th>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; vertical-align: center;" align="center">STT</th>
            <th style="font-weight: bold; vertical-align: center;" align="center">Mã nhân viên</th>
            <th style="font-weight: bold; vertical-align: center;" align="center">Tên nhân viên</th>
            <th style="font-weight: bold; vertical-align: center;" align="center">Ngày</th>
            @php
                $currentDate = $startDate->copy();
            @endphp
            @foreach(range(1, $dayInMonth) as $day)
                <th style="font-weight: bold;" align="center">{{ $currentDate->format('d') }}</th>
                @php
                    $currentDate->addDay();
                @endphp
            @endforeach
            <th style="font-weight: bold;" align="center">Ngày đi</th>
            <th style="font-weight: bold;" align="center">Trễ</th>
            <th style="font-weight: bold;" align="center">Sớm</th>
            <th style="font-weight: bold;" align="center">T.Giờ</th>
            <th style="font-weight: bold;" align="center">Ngày</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th style="font-weight: bold;" align="center">Thứ</th>
            @php
                $currentDate = $startDate->copy();
            @endphp
            @foreach(range(1, $dayInMonth) as $day)
                <th style="font-weight: bold;"
                    align="center">{{ $currentDate->format('D') === 'Sun' ? 'CN' : 'T'.($currentDate->dayOfWeek + 1) }}</th>
                @php
                    $currentDate->addDay();
                @endphp
            @endforeach
        </tr>
        @foreach($items as $item)
            <tr>
                <td rowspan="6" align="left" style="vertical-align: top;">{{ $loop->iteration }}</td>
                <td rowspan="6" align="left" style="vertical-align: top;">{{ $item['code'] }}</td>
                <td rowspan="6" align="left" style="vertical-align: top;">{{ $item['full_name'] }}</td>
                <td style="font-weight: bold;" align="left">Vào</td>
                @php
                    $currentDate = $startDate->copy();
                    $attendanceCount = 0;
                    $totalLateTime = 0;
                    $totalHours = 0;
                    $totalWorkTime = 0;
                    $cong = 0;
                @endphp
                @foreach(range(1, $dayInMonth) as $day)
                    @php
                        $attendanceFound = false;
                        $clockIn = '';
                        foreach ($item['attendances'] as $attendance) {
                            if ($attendance['date'] === $currentDate->format('Y-m-d') && isset($attendance['clock_in'])) {
                                $clockIn = substr($attendance['clock_in'], 0, 5);
                                $attendanceFound = true;
                                $attendanceCount++;

                                // Calculate late time
                                $clockInDateTime = new DateTime($clockIn);
                                $sevenThirty = new DateTime('07:30');
                                if ($clockInDateTime > $sevenThirty) {
                                    $lateInterval = $clockInDateTime->diff($sevenThirty);
                                    $lateSeconds = $lateInterval->s + $lateInterval->i * 60 + $lateInterval->h * 3600;
                                    $totalLateTime += $lateSeconds;
                                }

                                // Calculate work time
                                $workTime = $attendance['work_time'];
                                $workTimeInSeconds = strtotime($workTime) - strtotime('00:00:00');

                                if ($workTimeInSeconds >= 8 * 3600) {
                                    $workTimeInSeconds = 8 * 3600;
                                }

                                $totalWorkTime += $workTimeInSeconds;

                                // Calculate cong
                                if ($workTime !== '') {
                                    $workHours = explode(':', $workTime);
                                    $totalHours = intval($workHours[0]) + (intval($workHours[1]) / 60);
                                    if ($totalHours >= 8) {
                                        $cong += 1;
                                    } else {
                                        $cong = $cong + $totalHours / 8;
                                    }
                                }

                                break;
                            }
                        }
                    @endphp
                    <td align="center">{{ $attendanceFound ? $clockIn : '' }}</td>
                    @php
                        $currentDate->addDay();
                        $totalHours = floor($totalWorkTime / 3600);
                        $totalMinutes = floor(($totalWorkTime % 3600) / 60);
                        $totalSeconds = $totalWorkTime % 60;
                        $totalWorkTimeFormatted = sprintf('%02d:%02d', $totalHours, $totalMinutes);
                    @endphp
                @endforeach
                <td rowspan="6" align="center" style="vertical-align: top; font-weight: bold;">{{ $attendanceCount > 0 ? $attendanceCount: '' }}</td>
                <td rowspan="6" align="center" style="vertical-align: top; font-weight: bold;"> {{ $totalLateTime > 0 ? gmdate('H:i', $totalLateTime) : '' }}</td>
                <td rowspan="6" align="center" style="vertical-align: top; font-weight: bold;"></td>
                <td rowspan="6" align="center" style="vertical-align: top; font-weight: bold;">{{ $totalWorkTimeFormatted !== '00:00' ? $totalWorkTimeFormatted : '' }}</td>
                <td rowspan="6" align="center" style="vertical-align: top; font-weight: bold;">{{ $cong > 0 ? number_format($cong, 2) : '' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;" align="left">Ra</td>
                @php
                    $currentDate = $startDate->copy();
                @endphp
                @foreach(range(1, $dayInMonth) as $day)
                    @php
                        $attendanceFound = false;
                        $clockOut = '';
                        foreach ($item['attendances'] as $attendance) {
                            if ($attendance['date'] === $currentDate->format('Y-m-d')) {
                                $attendanceFound = true;
                                $clockOut = substr(strval($attendance['clock_out']), 0, 5);
                                break;
                            }
                        }
                    @endphp
                    <td align="center">{{ $attendanceFound ? $clockOut : '' }}</td>
                    @php
                        $currentDate->addDay();
                    @endphp
                @endforeach
            </tr>
            <tr>
                <td style="font-weight: bold;" align="left">Trễ</td>
                @php
                    $currentDate = $startDate->copy();
                @endphp
                @foreach(range(1, $dayInMonth) as $day)
                    @php
                        $lateTime = '';
                        foreach ($item['attendances'] as $attendance) {
                            if ($attendance['date'] === $currentDate->format('Y-m-d') && isset($attendance['clock_in'])) {
                                $clockIn = $attendance['clock_in'];
                                $clockInDateTime = new DateTime($clockIn);
                                $sevenThirty = new DateTime('07:30');
                                if ($clockInDateTime > $sevenThirty) {
                                    $lateInterval = $clockInDateTime->diff($sevenThirty);
                                    $lateTime = $lateInterval->format('%H:%I');
                                }
                                break;
                            }
                        }
                    @endphp
                    <td align="center">{{ $lateTime }}</td>
                    @php
                        $currentDate->addDay();
                    @endphp
                @endforeach
            </tr>
            <tr>
                <td style="font-weight: bold;" align="left">Sớm</td>
            </tr>
            <tr>
                <td style="font-weight: bold;" align="left">T.Giờ</td>
                @php
                    $currentDate = $startDate->copy();
                @endphp
                @foreach(range(1, $dayInMonth) as $day)
                    @php
                        $workTime = '';
                        foreach ($item['attendances'] as $attendance) {
                            if ($attendance['date'] === $currentDate->format('Y-m-d')) {
                                $workTime = substr($attendance['work_time'], 0, 5);
                                break;
                            }
                        }
                    @endphp
                    <td align="center">{{ $workTime }}</td>
                    @php
                        $currentDate->addDay();
                    @endphp
                @endforeach
            </tr>
            <tr>
                <td style="font-weight: bold;" align="left">Công</td>
                @php
                    $currentDate = $startDate->copy();
                @endphp
                @foreach(range(1, $dayInMonth) as $day)
                    @php
                        $workTime = '';
                        foreach ($item['attendances'] as $attendance) {
                            if ($attendance['date'] === $currentDate->format('Y-m-d')) {
                                $workTime = $attendance['work_time'];
                                break;
                            }
                        }

                        $cong = 0;
                        if ($workTime !== '') {
                            $workHours = explode(':', $workTime);
                            $totalHours = intval($workHours[0]) + (intval($workHours[1]) / 60);
                            if ($totalHours >= 8) {
                                $cong = 1;
                            } else {
                                $cong = round($totalHours / 8, 2);
                            }
                        }
                    @endphp
                    <td align="center">
                        {{ $cong }}
                    </td>
                    @php
                        $currentDate->addDay();
                    @endphp
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="7" align="center" style="font-weight: bold;">NGƯỜI LẬP</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="6" align="center" style="font-weight: bold;">GIÁM ĐỐC</td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="7" align="center" style="font-weight: bold;">ADMIN</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="6" align="center" style="font-weight: bold;">Admin MBW</td>
        </tr>
        </thead>
        <tbody>
        <tr>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
