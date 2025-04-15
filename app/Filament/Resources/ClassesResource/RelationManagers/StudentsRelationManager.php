<?php

namespace App\Filament\Resources\ClassesResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (Builder $query, $livewire) {
                        $class = $livewire->getOwnerRecord();
                        $academicYearId = $class->academic_year_id;
                    
                        return $query
                            ->whereDoesntHave('studentClasses.class.academicYear', function ($q) use ($academicYearId) {
                                $q->where('academic_years.id', $academicYearId);
                            })
                            ->orderBy('name', 'asc');
                    })                    
                    ->recordTitle(fn ($record) => $record->name)
                    ->recordSelectSearchColumns(['name'])
                    ->multiple()
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
