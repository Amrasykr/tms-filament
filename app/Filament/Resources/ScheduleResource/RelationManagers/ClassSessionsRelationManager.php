<?php

namespace App\Filament\Resources\ScheduleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassSessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'classSessions';

    protected static ?string $recordTitleAttribute = 'session_number';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('session_number')
                ->label('Nomor Sesi')
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('session_date')
                ->label('Tanggal Pelaksanaan')
                ->required()
                ->minDate(now())
                ->placeholder('Pilih Tanggal'),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->required()
                ->options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                ])
                ->default('pending'),
            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3)
                ->maxLength(65535)
                ->columnSpan(3),
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
