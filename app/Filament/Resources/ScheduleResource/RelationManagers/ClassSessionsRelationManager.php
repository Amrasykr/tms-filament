<?php

namespace App\Filament\Resources\ScheduleResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Set;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;

class ClassSessionsRelationManager extends RelationManager
{
    
    protected static string $relationship = 'classSessions';

    protected static ?string $recordTitleAttribute = 'session_number';
    
    protected static ?string $title = 'Sesi Kelas';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('session_number')
                    ->label('Nomor Sesi')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('session_date')
                    ->label('Tanggal Pelaksanaan')
                    ->required()
                    ->placeholder('Pilih Tanggal'),
    
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                    ])
                    ->live()
                    ->default('pending'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Fieldset::make('Daftar Kehadiran')
                    ->schema([
                        Repeater::make('attendances')
                            ->relationship('attendances')
                            ->disableLabel()
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('student_id')
                                        ->label('Nama Siswa')
                                        ->options(\App\Models\Student::all()->pluck('name', 'id'))
                                        ->disabled()
                                        ->dehydrated(false),
                
                                    Hidden::make('status_is_null')->dehydrated(false),
                
                                    Select::make('status')
                                        ->label('Status')
                                        ->required()
                                        ->hint(function ($state, $livewire, $get) {
                                            return $get('status_is_null') ? 'Belum di-set' : null;
                                        })
                                        ->hintColor('warning')
                                        ->afterStateHydrated(function (Set $set, $state) {
                                            if (is_null($state)) {
                                                $set('status', 'present');
                                                $set('status_is_null', true);
                                            } else {
                                                $set('status_is_null', false);
                                            }
                                        })
                                        ->options([
                                            'present' => 'Hadir',
                                            'absent' => 'Tidak Hadir',
                                            'sick' => 'Sakit',
                                            'permission' => 'Izin',
                                        ]),
                
                                    Textarea::make('notes')
                                        ->label('Catatan')
                                        ->rows(1)
                                        ->columnSpan(2),
                                ]),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->columnSpanFull()
                            ->disabled(fn (?Model $record) => $record === null),
                    ])
                    ->extraAttributes([
                        'style' => 'max-height: 400px; overflow-y: auto;',
                    ])
                    ->hidden(fn (Get $get) => $get('status') === 'pending'),
                
                ]);
    }

    public function table(Table $table): Table
    {
        $count = $this->getRelationship()->count(); 

        return $table
            ->heading("Sesi Kelas Terdaftar ($count)")
            ->columns([
                Tables\Columns\TextColumn::make('session_number')
                    ->label('Nomor'),
                Tables\Columns\TextColumn::make('session_date')
                    ->label('Tanggal Pelaksanaan')
                    ->dateTime('d F Y'),
                Tables\Columns\TextColumn::make('status')
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'Selesai',
                        'pending' => 'Terjadwal',
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('rekapAbsensi')
                    ->label('Rekap Absensi')
                    ->icon('heroicon-o-chart-bar')
                    ->action(fn () => null)
                    ->modalHeading('Rekap Absensi')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('7xl')
                    ->modalContent(function (RelationManager $livewire) {
                        $schedule = $livewire->getOwnerRecord();
                        $sessions = $schedule->classSessions()->orderBy('session_number')->get();
                        $students = $schedule->class->students;
            
                        return view('filament.attendance-recap', compact('sessions', 'students', 'schedule'));
                    }),

                Tables\Actions\Action::make('exportAbsensi')
                    ->label('Export Absensi')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (RelationManager $livewire) {
                        $schedule = $livewire->getOwnerRecord();
                        $className = preg_replace('/[\/\\\\]/', '-', $schedule->class->name);
                        $subjectName = preg_replace('/[\/\\\\]/', '-', $schedule->subject->name);
                        $fileName = "Absensi_{$className}_{$subjectName}.xlsx";

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\ScheduleAttendanceExport($schedule),
                            $fileName
                        );
                }),
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


