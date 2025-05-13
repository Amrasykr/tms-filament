<?php

namespace App\Filament\Teacher\Resources\ScheduleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GradesRelationManager extends RelationManager
{
    protected static string $relationship = 'Grades';

    protected static ?string $recordTitleAttribute = 'student_id';

    protected static ?string $title = 'Penilaian';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('attendance_score')
                    ->label('Nilai Kehadiran')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\TextInput::make('task_score')
                    ->label('Nilai Tugas')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\TextInput::make('midterm_score')
                    ->label('Nilai UTS')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\TextInput::make('final_exam_score')
                    ->label('Nilai UAS')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                
            ]);
    }


    public function table(Table $table): Table
    {
        $count = $this->getRelationship()->count(); 

        return $table
            ->recordTitleAttribute('student_id')
            ->heading('Jumlah Murid : ' . $count)
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Murid'),
                Tables\Columns\TextColumn::make('attendance_score')
                    ->label('Nilai Kehadiran'),
                Tables\Columns\TextColumn::make('task_score')
                    ->label('Nilai Tugas'),
                Tables\Columns\TextColumn::make('midterm_score')
                    ->label('Nilai UTS'),
                Tables\Columns\TextColumn::make('final_exam_score')
                    ->label('Nilai UAS'),
                
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('generateGrades')
                ->label('Buat Nilai Dasar')
                ->icon('heroicon-m-plus-circle')
                ->requiresConfirmation()
                ->color('success')
                ->action(function () {
                    $schedule = $this->getOwnerRecord();
                    $studentIds = \App\Models\StudentClass::where('class_id', $schedule->class_id)->pluck('student_id');
                
                    foreach ($studentIds as $studentId) {
                        \App\Models\Grade::firstOrCreate([
                            'student_id' => $studentId,
                            'schedule_id' => $schedule->id,
                        ], [
                            'attendance_score' => 0,
                            'task_score' => 0,
                            'midterm_score' => 0,
                            'final_exam_score' => 0,
                        ]);
                    }
                
                    Notification::make()
                        ->title('Sukses')
                        ->body('Nilai dasar berhasil digenerate untuk semua siswa.')
                        ->success()
                        ->send();
                })
                ->hidden(function () {
                    $schedule = $this->getOwnerRecord();
                    return \App\Models\Grade::where('schedule_id', $schedule->id)->exists();
                })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
