<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYears;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;


class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null; // nggak perlu auto-refresh

    protected ?string $heading = 'Data Utama';


    protected function getCards(): array
    {
        $currentAcademicYear = AcademicYears::where('status', 'active')->first();

        return [
            Card::make('Total Guru Aktif', Teacher::where('status', 'active')->count())
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->description('Semua guru yang aktif'),

            Card::make('Total Murid Aktif', Student::where('status', 'active')->count())
                ->icon('heroicon-o-users')
                ->description('Semua murid yang aktif'),

            Card::make('Total Jadwal', $currentAcademicYear
                ? Schedule::where('academic_year_id', $currentAcademicYear->id)->count()
                : 0)
                ->icon('heroicon-o-calendar-days')
                ->description($currentAcademicYear
                    ? 'Tahun Ajaran: ' . $currentAcademicYear->name
                    : 'Belum ada tahun ajaran aktif'),
        ];
    }
}