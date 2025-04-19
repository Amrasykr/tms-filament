<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    // protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User';

    protected static ?string $label = 'Guru';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Card::make()
                            ->schema([
                                Forms\Components\FileUpload::make('profile')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                    ->maxSize('512')
                                    ->directory('teachers')
                                    ->imagePreviewHeight('250'),
                            ])
                            ->columnSpan(1),
    
                        Grid::make(2) 
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nip')
                                    ->label('NIP')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email()
                                    ->unique(Teacher::class)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('No. HP')
                                    ->maxLength(255),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Aktif',
                                        'inactive' => 'Tidak Aktif',
                                    ])
                                    ->default('active')
                                    ->required(),
                                Forms\Components\Textarea::make('address')
                                    ->label('Alamat')
                                    ->rows(3)                                  
                                    ->maxLength(255)
                                    ->columnSpan(2),
                            ])
                            ->columnSpan(2),
                    ]),
            ]);
    }
    
    

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile')
                ->label('')
                ->circular()
                ->rounded()
                ->size(50)
                ->getStateUsing(function ($record) {
                    return $record->profile
                        ? asset('storage/' . $record->profile)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=random';
                }),                   
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')     
                    ->searchable()          
                    ->sortable(),
                Tables\Columns\TextColumn::make('nip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('No. Telepon'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('status')
                ->multiple()
                ->options([
                    'active' => 'Aktif',
                    'inactive' => 'Tidak Aktif',
                ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
