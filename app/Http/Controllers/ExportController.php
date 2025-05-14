<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ScheduleAttendanceExport;
use App\Exports\ScheduleGradeExport;
use App\Models\Schedule;
use Maatwebsite\Excel\Facades\Excel;


class ExportController extends Controller
{
    public function exportAttendance(Schedule $schedule)
    {
        $fileName = 'Absensi_' . $schedule->class->name . '_' . $schedule->subject->name . '.xlsx';
        return Excel::download(new ScheduleAttendanceExport($schedule), $fileName);
    }

    public function exportGrades($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $fileName = 'Nilai_' . preg_replace('/[\/\\]/', '-', $schedule->class->name) . '.xlsx';
        return Excel::download(new ScheduleGradeExport($schedule), $fileName);
    }
}
