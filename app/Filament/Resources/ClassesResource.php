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
                    ->label('Nama Kelas')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Kode Kelas')
                    ->required(),
                Forms\Components\Select::make('academic_year_id')
                    ->relationship(name: 'academicYear', titleAttribute: 'name')
                    ->label('Tahun Akademik')
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->relationship(
                        name: 'homeroomTeacher',
                        titleAttribute: 'name'
                    )
                    ->label('Wali Kelas')
                    ->default(null),
                Forms\Components\Select::make('major_id')
                    ->relationship(
                        name: 'major',
                        titleAttribute: 'name'
                    )
                    ->label('Jurusan')
                    ->default(null),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYear.status')
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    })
                    ->badge()
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('major.name')
                    ->color(fn (string $state): string => match ($state) {
                        'IPA' => 'info',
                        'IPS' => 'primary',
                    })

                    ->badge()
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('homeroomTeacher.name')
                    ->label('Wali Kelas')
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
                // SelectFilter::make('major_id')
                //     ->label('Jurusan')
                //     ->options(fn () => \App\Models\Major::pluck('name', 'id')->toArray())
                //     ->query(function ($query, $state) {
                //         return $query->where('major_id', $state);
                //     })
                //     ->placeholder('Pilih Jurusan'),
            
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
