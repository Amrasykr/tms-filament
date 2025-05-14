<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduleGradeExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function collection()
    {
        $students = $this->schedule->class->students;
        $data = [];

        foreach ($students as $index => $student) {
            $grades = $this->schedule->grades()->where('student_id', $student->id)->first();
            $data[] = [
                'no' => $index + 1,
                'name' => $student->name,
                'nis' => $student->nis,
                'attendance_score' => $grades->attendance_score ?? '-',
                'task_score' => $grades->task_score ?? '-',
                'midterm_score' => $grades->midterm_score ?? '-',
                'final_exam_score' => $grades->final_exam_score ?? '-',
                'final_score' => $grades->final_score ?? '-',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['No', 'Nama Murid', 'NIS', 'Nilai Kehadiran', 'Nilai Tugas', 'Nilai UTS', 'Nilai UAS', 'Nilai Akhir'];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
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

        // Set border untuk seluruh tabel
        $sheet->getStyle("A1:{$lastColumn}" . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        return $sheet;
    }

    public function title(): string
    {
        // Mengganti karakter "/" dan "\" dengan "-"
        $className = preg_replace('/[\/\\\\]/', '-', $this->schedule->class->name);
        return "Nilai {$className}";
    }
}
