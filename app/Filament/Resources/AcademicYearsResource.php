<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearsResource\Pages;
use App\Models\AcademicYears;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class AcademicYearsResource extends Resource
{
    protected static ?string $model = AcademicYears::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $label = 'Tahun Akademik';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(AcademicYears::class)
                    ->maxLength(255),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\Select::make('semester')
                    ->options([
                        'ganjil' => 'Ganjil',
                        'genap' => 'Genap',
                    ])
                    ->required(),
                Radio::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->inline()
                    ->default('active')
                    ->inlineLabel(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester')
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'ganjil' => 'info',
                        'genap' => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ganjil' => 'Ganjil',
                        'genap' => 'Genap',
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    })
                    ->badge(),
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
                SelectFilter::make('semester')
                ->options([
                    'ganjil' => 'Ganjil',
                    'genap' => 'Genap',
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
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYears::route('/create'),
            'edit' => Pages\EditAcademicYears::route('/{record}/edit'),
        ];
    }
}
