<?php

// app/Exports/StudentGradesExport.php

namespace App\Exports;

use App\Models\Schedule;
use App\Models\Student;
use App\Models\Grade;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentGradesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $studentId;
    protected $academicYearId;

    public function __construct($studentId, $academicYearId)
    {
        $this->studentId = $studentId;
        $this->academicYearId = $academicYearId;
    }

    public function collection()
    {
        $grades = Grade::query()
            ->where('student_id', $this->studentId)
            ->whereHas('schedule', function ($query) {
                $query->where('academic_year_id', $this->academicYearId);
            })
            ->with(['schedule.subject', 'schedule.teacher'])
            ->get();

        $data = [];
        foreach ($grades as $index => $grade) {
            $data[] = [
                'no' => $index + 1,
                'subject' => $grade->schedule->subject->name ?? 'N/A',
                'teacher' => $grade->schedule->teacher->name ?? 'N/A',
                'attendance_score' => $grade->attendance_score,
                'task_score' => $grade->task_score,
                'midterm_score' => $grade->midterm_score,
                'final_exam_score' => $grade->final_exam_score,
                'final_score' => $grade->final_score,
            ];
        }

        // Hitung nilai rata-rata
        $averageScore = $grades->avg('final_score');
        $data[] = [
            'no' => '',
            'subject' => '',
            'teacher' => '',
            'attendance_score' => '',
            'task_score' => '',
            'midterm_score' => '',
            'final_exam_score' => 'Rata-Rata',
            'final_score' => number_format($averageScore, 2),
        ];

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Pelajaran',
            'Pengajar',
            'Nilai Absensi',
            'Nilai Tugas',
            'Nilai Midterm',
            'Nilai Final Exam',
            'Final Score',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $headerRange = "A1:{$lastColumn}1";
        $dataRange = "A2:{$lastColumn}" . ($sheet->getHighestRow() - 1);
        $averageRange = "A" . $sheet->getHighestRow() . ":{$lastColumn}" . $sheet->getHighestRow();

        // Styling untuk header
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
                'name' => 'Roboto',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Styling untuk data (value)
        $sheet->getStyle($dataRange)->applyFromArray([
            'font' => [
                'size' => 11,
                'name' => 'Roboto',
            ],
        ]);

        // Styling untuk rata-rata
        $sheet->getStyle($averageRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Roboto',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F3F3'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Set border untuk seluruh tabel
        $sheet->getStyle("A1:{$lastColumn}" . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Lebarkan kolom Nama Pelajaran dan Pengajar
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);

        return $sheet;
    }

    public function title(): string
    {
        $student = Student::find($this->studentId);
        return "Nilai - {$student->name}";
    }
}