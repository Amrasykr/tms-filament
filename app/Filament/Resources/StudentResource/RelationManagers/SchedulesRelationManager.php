<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\AcademicYears;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;


class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Jadwal yang Diampu';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('class_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('class_id')
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scheduleTime')
                    ->label('Waktu')
                    ->getStateUsing(function ($record) {
                        $dayMap = [
                            'Monday' => 'Senin',
                            'Tuesday' => 'Selasa',
                            'Wednesday' => 'Rabu',
                            'Thursday' => 'Kamis',
                            'Friday' => 'Jumat',
                            'Saturday' => 'Sabtu',
                            'Sunday' => 'Minggu',
                        ];

                        $day = $dayMap[$record->scheduleTime->day] ?? $record->scheduleTime->day;
                        return "{$day}, {$record->scheduleTime->start_time} - {$record->scheduleTime->end_time}";
                    }),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Pengajar')
                    ->searchable(),
                    
            ])
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(
                        \App\Models\AcademicYears::orderBy('start_date', 'desc')->pluck('name', 'id')
                    ),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Action::make('exportGrades')
                    ->label('Export Nilai Keseluruhan')
                    ->button()
                    ->form([
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Tahun Akademik')
                            ->options(AcademicYears::pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $studentId = $this->ownerRecord->id;
                        $academicYearId = $data['academic_year_id'];

                        // Redirect ke route untuk melakukan export
                        return redirect()->route('students.export-grades', [
                            'student' => $studentId,
                            'academic_year' => $academicYearId,
                        ]);
                    })
                    ->button()
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->requiresConfirmation(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
