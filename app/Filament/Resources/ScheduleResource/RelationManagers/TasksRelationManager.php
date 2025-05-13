<?php

namespace App\Filament\Resources\ScheduleResource\RelationManagers;

use Closure;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Tugas';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Tugas')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('due_date')
                    ->label('Tanggal Pengumpulan')
                    ->required()
                    ->placeholder('Pilih Tanggal'),

                Forms\Components\Select::make('class_session_id')
                    ->label('Untuk Sesi')
                    ->required()
                    ->relationship('classSession', 'session_number')
                    ->options(function (callable $get) {
                    // Ambil schedule_id dari relationship
                    $scheduleId = $this->ownerRecord->id ?? null;
                    
                    // Jika tidak ada schedule_id, kembalikan array kosong
                    if (!$scheduleId) return [];

                    // Ambil semua sesi yang sesuai dengan schedule_id
                    return \App\Models\ClassSessions::where('schedule_id', $scheduleId)
                        ->pluck('session_number', 'id')
                        ->toArray();
                }),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Fieldset::make('Daftar Penilaian')
                    ->schema([
                        Repeater::make('student_tasks')
                            ->disableLabel()
                            ->relationship('studentTasks')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('student_id')
                                        ->label('Nama Siswa')
                                        ->options(\App\Models\Student::all()->pluck('name', 'id'))
                                        ->disabled()
                                        ->dehydrated(false),

                                    TextInput::make('score')
                                        ->label('Nilai')
                                        ->numeric(),

                                    Textarea::make('comment')
                                        ->label('Catatan')
                                        ->rows(1)
                                        ->columnSpan(2),
                                ]),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->columnSpanFull()
                    ])
                    ->extraAttributes([
                        'style' => 'max-height: 400px; overflow-y: auto;',
                    ])
                    ->hidden(fn (Get $get) => $get('id') === null)            
                ]);
    }

    public function table(Table $table): Table
    {
        $count = $this->getRelationship()->count(); 


        return $table
            ->recordTitleAttribute('name')
            ->heading('Jumlah Tugas : ' . $count)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tugas'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('classSession.session_number')
                    ->label('Sesi'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Tanggal Pengumpulan')
                    ->dateTime('d F Y'),
                
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
