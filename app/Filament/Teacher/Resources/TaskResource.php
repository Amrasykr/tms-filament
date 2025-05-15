<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\TaskResource\Pages;
use App\Filament\Teacher\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pembelajaran';

    protected static ?string $label = 'Tugas';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $teacher = Filament::auth()->user();

        return $form
            ->schema([
                Select::make('schedule_id')
                    ->label('Jadwal')
                    ->options(function () use ($teacher) {
                        return \App\Models\Schedule::query()
                            ->where('teacher_id', $teacher->id)
                            ->with('subject')
                            ->get()
                            ->pluck('subject.name', 'id')
                            ->toArray();
                    })
                    ->reactive() // biar trigger perubahan
                    ->required()
                    ->afterStateUpdated(fn (callable $set) => $set('class_session_id', null)),

                Select::make('class_session_id')
                    ->label('Sesi Kelas')
                    ->options(function (callable $get) {
                        $scheduleId = $get('schedule_id');
                        if (!$scheduleId) {
                            return [];
                        }
                        return \App\Models\ClassSessions::where('schedule_id', $scheduleId)
                            ->pluck('session_number', 'id')
                            ->toArray();
                    })
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Tugas')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('schedule.subject.name')
                    ->label('Mata Pelajaran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule.class.name')
                    ->label('Kelas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classSession.session_number')
                    ->label('Sesi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tugas')
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Terakhir Pengumpulan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
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
                //
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
