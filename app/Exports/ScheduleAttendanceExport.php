<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduleAttendanceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function collection()
    {
        $students = $this->schedule->class->students;
        $sessions = $this->schedule->classSessions()->orderBy('session_number')->get();

        $data = [];
        foreach ($students as $index => $student) {
            $row = [
                'no' => $index + 1,
                'name' => $student->name,
                'nis' => $student->nis,
            ];

            foreach ($sessions as $session) {
                $attendance = $session->attendances->firstWhere('student_id', $student->id);
                $row["session_{$session->session_number}"] = $attendance->status ?? '-';
            }

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $sessionHeaders = $this->schedule->classSessions()->orderBy('session_number')->get()
            ->map(fn ($session) => 'Sesi ' . $session->session_number)
            ->toArray();

        return array_merge(['No', 'Nama Murid', 'NIS'], $sessionHeaders);
    }

    public function styles(Worksheet $sheet)
    {
        // Tentukan range header
        $lastColumn = $sheet->getHighestColumn();
        $headerRange = "A1:{$lastColumn}1";
        $dataRange = "A2:{$lastColumn}" . $sheet->getHighestRow();

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

        // Lebarkan kolom Nama Murid dan NIS
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);

        // Set border untuk seluruh tabel
        $sheet->getStyle(
            "A1:{$lastColumn}" . $sheet->getHighestRow()
        )->applyFromArray([
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
        // Nama sheet berdasarkan nama kelas
        $className = preg_replace('/[\/\\\\]/', '-', $this->schedule->class->name);
        return "Absensi {$className}";
    }
}