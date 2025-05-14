<?php

namespace App\Filament\Resources\TeacherResourcesResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Jadwal yang Diambil';


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
                Tables\Columns\TextColumn::make('class.name')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scheduleTime')
                    ->label('Waktu')
                    ->toggleable(isToggledHiddenByDefault: true)
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
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progres')
                    ->getStateUsing(function ($record) {
                        $totalSessions = $record->classSessions()->count();
                        $completedSessions = $record->classSessions()->where('status', 'completed')->count();

                        if ($totalSessions === 0) {
                            return '0% (0/0)';
                        }

                        $progress = round(($completedSessions / $totalSessions) * 100, 2);
                        return "{$progress}% ({$completedSessions}/{$totalSessions})";
                    })
                    ->sortable(),
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
