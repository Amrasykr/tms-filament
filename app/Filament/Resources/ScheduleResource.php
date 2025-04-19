<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
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

    protected static ?string $navigationGroup = 'Jadwal';

    protected static ?string $label = 'Jadwal Pembelajaran';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('class_id')
                    ->label('Kelas')
                    ->options(function () {
                        return \App\Models\Classes::whereHas('academicYear', function ($query) {
                                $query->where('status', 'active');
                            })
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Subject::all()->mapWithKeys(function ($item) {
                            return [
                                $item->id => "{$item->name} - {$item->code}",
                            ];
                        });
                    })
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->label('Pengajar')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Teacher::where(function ($query) {
                                $query->where('status', 'active');
                            })
                            ->pluck('name', 'id');
                    })
                    ->required(),
                    Forms\Components\Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(function () {
                        return \App\Models\AcademicYears::where('status', 'active')
                            ->pluck('name', 'id');
                    })
                    ->default(function () {
                        return \App\Models\AcademicYears::where('status', 'active')->value('id');
                    })
                    ->required(),         
                    Forms\Components\Select::make('schedule_time_id')
                        ->label('Waktu Pembelajaran')
                        ->options(function () {
                            return \App\Models\SchedulesTime::all()->mapWithKeys(function ($item) {
                                return [
                                    $item->id => "{$item->day}, {$item->start_time} - {$item->end_time}",
                                ];
                            });
                        })
                        ->required()
                        ->searchable()
                        ->rule(function (Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $scheduleTimeId = $value;
                                $classId = $get('class_id');
                                $teacherId = $get('teacher_id');
                                $academicYearId = $get('academic_year_id');
                                $recordId = $get('__record')?->id;
                        
                                if (!$scheduleTimeId || !$classId || !$teacherId || !$academicYearId) {
                                    return;
                                }
                        
                                // Cek bentrok kelas dalam tahun ajaran yang sama
                                $conflictClass = \App\Models\Schedule::where('schedule_time_id', $scheduleTimeId)
                                    ->where('class_id', $classId)
                                    ->where('academic_year_id', $academicYearId)
                                    ->when($recordId, fn ($query) => $query->where('id', '!=', $recordId))
                                    ->exists();
                        
                                if ($conflictClass) {
                                    $fail('Kelas ini sudah memiliki jadwal di waktu tersebut untuk tahun ajaran ini.');
                                    return;
                                }
                        
                                // Cek bentrok guru dalam tahun ajaran yang sama
                                $conflictTeacher = \App\Models\Schedule::where('schedule_time_id', $scheduleTimeId)
                                    ->where('teacher_id', $teacherId)
                                    ->where('academic_year_id', $academicYearId)
                                    ->when($recordId, fn ($query) => $query->where('id', '!=', $recordId))
                                    ->exists();
                        
                                if ($conflictTeacher) {
                                    $fail('Guru ini sudah memiliki jadwal di waktu tersebut untuk tahun ajaran ini.');
                                }
                            };
                        })
                        ,
                    Forms\Components\Fieldset::make('Sesi Berulang')
                        ->schema([
                            Forms\Components\Toggle::make('is_repeating')
                                ->label('Aktifkan sesi berulang?')
                                ->live()
                                ->disabled(fn (?Model $record) => filled($record))
                                ->visible(fn ($get) => !$get('record') || !$get('record')->exists),
                            Forms\Components\TextInput::make('number_of_sessions')
                                ->label('Jumlah sesi')
                                ->numeric()
                                ->visible(fn ($get) => $get('is_repeating') && (!$get('record') || !$get('record')->exists))
                                ->required(fn (Forms\Get $get) => $get('is_repeating'))
                                ->disabled(fn (?Model $record) => filled($record))
                        ])
                        ->hidden(fn (?Model $record) => $record && $record->exists) // Menyembunyikan seluruh fieldset saat update
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('class.name')
                    ->label('Kelas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Pengajar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran'),
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
            RelationManagers\ClassSessionsRelationManager::class,
            RelationManagers\GradesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
