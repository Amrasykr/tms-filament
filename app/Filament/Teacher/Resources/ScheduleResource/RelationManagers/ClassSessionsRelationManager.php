<?php

namespace App\Filament\Teacher\Resources\ScheduleResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ClassSessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'ClassSessions';

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
