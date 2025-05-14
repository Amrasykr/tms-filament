<?php

namespace App\Filament\Resources\ScheduleResource\RelationManagers;

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
    protected static string $relationship = 'grades';

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
                Forms\Components\TextInput::make('final_score')
                    ->label('Nilai Akhir')
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
                Tables\Columns\TextColumn::make('final_score')
                    ->label('Nilai Akhir'),
                
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
                    }),

                Tables\Actions\Action::make('accumulateFinalScores')
                    ->label('Hitung Nilai Akhir')
                    ->icon('heroicon-o-calculator')
                    ->requiresConfirmation()
                    ->color('primary')
                    ->action(function () {
                        $schedule = $this->getOwnerRecord();

                        // Cek semua sesi kelas sudah selesai
                        $pendingSessions = $schedule->classSessions()->where('status', 'pending')->exists();
                        if ($pendingSessions) {
                            Notification::make()
                                ->title('Gagal Menghitung')
                                ->body('Masih ada sesi kelas yang berstatus pending. Selesaikan dulu semua sesi.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Cek semua tugas sudah selesai
                        $incompleteTasks = $schedule->tasks()
                            ->whereDate('due_date', '>', now())
                            ->exists();
                        if ($incompleteTasks) {
                            Notification::make()
                                ->title('Gagal Menghitung')
                                ->body('Masih ada tugas yang belum melewati due date. Selesaikan dulu semua tugas.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Cek semua nilai midterm dan final exam sudah terisi
                        $incompleteScores = $schedule->grades()
                            ->where(function ($query) {
                                $query->whereNull('midterm_score')
                                    ->orWhere('midterm_score', 0.00)
                                    ->orWhereNull('final_exam_score')
                                    ->orWhere('final_exam_score', 0.00);
                            })
                            ->exists();
                        if ($incompleteScores) {
                            Notification::make()
                                ->title('Gagal Menghitung')
                                ->body('Masih ada nilai UTS atau UAS yang belum diisi.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Ambil bobot dari schedule
                        $attendanceWeight = $schedule->attendance_weight;
                        $taskWeight = $schedule->task_weight;
                        $midtermWeight = $schedule->midterm_weight;
                        $finalExamWeight = $schedule->final_exam_weight;

                        // Pastikan bobot total adalah 100.00
                        $totalWeight = $schedule->attendance_weight + $schedule->task_weight + $schedule->midterm_weight + $schedule->final_exam_weight;

                        if (bccomp($totalWeight, '100.00', 2) !== 0) {
                            Notification::make()
                                ->title('Gagal Menghitung')
                                ->body('Total bobot harus 100.00% sebelum menghitung nilai akhir.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Update final_score untuk setiap murid
                        foreach ($schedule->grades as $grade) {
                            $finalScore = 
                                ($grade->attendance_score * $attendanceWeight / 100.00) +
                                ($grade->task_score * $taskWeight / 100.00) +
                                ($grade->midterm_score * $midtermWeight / 100.00) +
                                ($grade->final_exam_score * $finalExamWeight / 100.00);

                            // Simpan final score
                            $grade->update(['final_score' => bcadd($finalScore, '0', 2)]);
                        }

                        Notification::make()
                            ->title('Sukses')
                            ->body('Nilai akhir berhasil dihitung untuk semua siswa.')
                            ->success()
                            ->send();
                }),

                Tables\Actions\Action::make('exportGrades')
                    ->label('Export Nilai')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (RelationManager $livewire) {
                        $schedule = $livewire->getOwnerRecord();
                        $className = preg_replace('/[\/\\\\]/', '-', $schedule->class->name);
                        $subjectName = preg_replace('/[\/\\\\]/', '-', $schedule->subject->name);
                        $fileName = "Nilai_{$className}_{$subjectName}.xlsx";

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\ScheduleGradeExport($schedule),
                            $fileName
                        );
                 }),

                    
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
}
