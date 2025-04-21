<?php

namespace App\Filament\Teacher\Pages;

use App\Models\Teacher;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;

class TeacherProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Profil Guru';
    protected static ?string $title = 'Profil Guru';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.teacher-profile';

    public ?array $data = [];
    public Teacher $teacher;

    public function mount(): void
    {
        $this->teacher = Filament::auth()->user();

        if (!$this->teacher) {
            abort(403);
        }

        $this->data = $this->teacher->toArray();
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->teacher)
            ->statePath('data')
            ->schema([
                Grid::make(3)->schema([
                    Group::make()
                        ->schema([
                            Section::make('Foto & Info Akun')
                                ->schema([
                                    FileUpload::make('profile')
                                        ->label('Foto Profil')
                                        ->directory('teachers')
                                        ->image()
                                        ->maxSize('512')
                                        ->acceptedFileTypes(['image/jpeg', 'image/png']) 
                                        ->imagePreviewHeight('250'),

                                    Placeholder::make('created_at')
                                        ->label('Dibuat')
                                        ->content(fn ($get) => Carbon::parse($get('created_at'))->translatedFormat('d F Y H:i')),

                                    Placeholder::make('updated_at')
                                        ->label('Terakhir Diubah')
                                        ->content(fn ($get) => Carbon::parse($get('updated_at'))->translatedFormat('d F Y H:i')),
                                ])
                                ->columns(1),
                        ])
                        ->columnSpan(1),

                    Group::make()
                        ->schema([
                            Section::make('Ubah Data')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nama Lengkap')
                                        ->required(),

                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->unique(ignoreRecord: true)
                                        ->required(),

                                    TextInput::make('nip')
                                        ->label('NIP')
                                        ->nullable(),

                                    TextInput::make('phone')
                                        ->label('Nomor HP')
                                        ->nullable(),

                                    TextInput::make('address')
                                        ->label('Alamat')
                                        ->nullable(),

                                    Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'active' => 'Aktif',
                                            'inactive' => 'Tidak Aktif',
                                        ])
                                        ->required(),

                                    TextInput::make('password')
                                        ->label('Password Baru')
                                        ->password()
                                        ->revealable()
                                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                        ->dehydrated(fn ($state) => filled($state)),
                                ])
                                ->columns(2),
                        ])
                        ->columnSpan(2),
                ]),
            ]);
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $this->teacher->update($data);
        } catch (Halt $exception) {
            return;
        }

        if (request()->hasSession() && isset($this->teacher->password)) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $this->teacher->password,
            ]);
        }

        $this->data['password'] = null;

        Notification::make()
            ->title('Berhasil')
            ->body('Profil berhasil diperbarui.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action('save') 
                ->color('primary'),
        ];
    }
}
