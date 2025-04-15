<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassesResource\Pages;
use App\Filament\Resources\ClassesResource\RelationManagers;
use App\Filament\Resources\ClassesResource\RelationManagers\StudentsRelationManager;
use App\Models\Classes;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClassesResource extends Resource
{
    protected static ?string $model = Classes::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $label = 'Kelas';

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->placeholder('7B 2024/2025')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required(),
                Forms\Components\Select::make('academic_year_id')
                    ->relationship(name: 'academicYear', titleAttribute: 'name')
                    ->label('Academic Year')
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->relationship(
                        name: 'homeroomTeacher',
                        titleAttribute: 'name'
                    )
                    ->label('Homeroom Teacher')
                    ->default(null),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYear.status')
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    })
                    ->badge()
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('homeroomTeacher.name')
                    ->label('Homeroom Teacher')
                    ->sortable(),
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
                ->label('Status')
                ->options(fn () => \App\Models\AcademicYears::pluck('status', 'status')->toArray())
                ->query(function ($query, $state) {
                    return $query->whereHas('academicYear', function ($query) use ($state) {
                        $query->where('status', $state);
                    });
                })
                ->default('active'),
            
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
            StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClasses::route('/create'),
            'edit' => Pages\EditClasses::route('/{record}/edit'),
        ];
    }
}
