<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\ScheduleResource\Pages;
use App\Filament\Teacher\Resources\ScheduleResource\RelationManagers;
use App\Filament\Teacher\Resources\ScheduleResource\RelationManagers\ClassSessionsRelationManager;
use App\Filament\Teacher\Resources\ScheduleResource\RelationManagers\GradesRelationManager;
use App\Filament\Teacher\Resources\ScheduleResource\RelationManagers\TasksRelationManager;
use App\Models\ClassSessions;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pembelajaran';

    protected static ?string $label = 'Jadwal';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
   {
      return false;
   }




    public static function form(Form $form): Form
    {
        return $form 
            ->schema([
                Forms\Components\Fieldset::make('Bobot Penilaian')
                    ->schema([
                        Forms\Components\TextInput::make('attendance_weight')
                            ->label('Bobot Kehadiran (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(25)
                            ->required(),
                            
                        Forms\Components\TextInput::make('task_weight')
                            ->label('Bobot Tugas (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(25)
                            ->required(),
                        
                        Forms\Components\TextInput::make('midterm_weight')
                            ->label('Bobot UTS (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(25)
                            ->required(),
                        
                        Forms\Components\TextInput::make('final_exam_weight')
                            ->label('Bobot UAS (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(25)
                            ->required(),
                        
                        Forms\Components\Placeholder::make('weight_total')
                            ->label('Total Bobot')
                            ->content(fn (Forms\Get $get) => 
                                ($get('attendance_weight') ?? 0) + 
                                ($get('task_weight') ?? 0) + 
                                ($get('midterm_weight') ?? 0) + 
                                ($get('final_exam_weight') ?? 0) . '%'
                            )
                            ->reactive()
                            ->disableLabel(),
                    ])
                    ->columns(2)
                    ->label('Bobot Penilaian'),
                ]);
            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('class.name')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
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
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('academic_year_id')
                ->label('Tahun Ajaran')
                ->options(
                    \App\Models\AcademicYears::orderBy('start_date', 'desc')->pluck('name', 'id')
                ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ClassSessionsRelationManager::class,
            GradesRelationManager::class,
            TasksRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            // 'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
