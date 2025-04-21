<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AttendanceExport implements FromArray, WithHeadings, WithEvents
{
    protected $schedule;
    protected $sessionDates;

    public function __construct($scheduleId)
    {
        $this->schedule = Schedule::with([
            'subject', 'class', 'teacher', 'academicYear', 'scheduleTime',
            'classSessions.attendances.student'
        ])->findOrFail($scheduleId);
    
        $this->sessionDates = $this->schedule->classSessions
            ->sortBy('session_date')
            ->pluck('session_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M Y'))
            ->values();
    }
    

    public function array(): array
    {
        $classSessions = $this->schedule->classSessions->sortBy('session_date');
    
        $students = collect();
        foreach ($classSessions as $session) {
            foreach ($session->attendances as $attendance) {
                $students->push($attendance->student);
            }
        }
        $students = $students->unique('id')->sortBy('name')->values();
    
        $rows = [];
    
        foreach ($students as $student) {
            $row = [$student->name];
    
            foreach ($classSessions as $session) {
                $attendance = $session->attendances->firstWhere('student_id', $student->id);
                $row[] = $attendance ? ucfirst($attendance->status) : '-';
            }
    
            $rows[] = $row;
        }
    
        return $rows;
    }
    

    public function headings(): array
    {
        return array_merge(['Nama Siswa'], $this->sessionDates->toArray());
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $meta = [
                    ['Mata Pelajaran', $this->schedule->subject->name ?? '-'],
                    ['Pengajar', $this->schedule->teacher->name ?? '-'],
                    ['Jam Pelajaran', $this->schedule->scheduleTime->start_time . ' - ' . $this->schedule->scheduleTime->end_time],
                    ['Kelas', $this->schedule->class->name ?? '-'],
                    ['Tahun Akademik', $this->schedule->academicYear->name ?? '-'],
                ];

                $metaCount = count($meta);
                $sheet->insertNewRowBefore(1, $metaCount + 1); 

                foreach ($meta as $index => $row) {
                    $sheet->setCellValue('A' . ($index + 1), $row[0]);
                    $sheet->setCellValue('B' . ($index + 1), $row[1]);
                }
            },
        ];
    }
}