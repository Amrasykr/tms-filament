<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Exceptions\Halt;

class AdminProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profil Admin';
    protected static ?string $title = 'Profil Admin';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.admin-profile';

    public ?array $data = [];
    public User $user;

    public function mount(): void
    {
        $this->user = Filament::auth()->user();

        if (! $this->user) {
            abort(403);
        }

        $this->data = $this->user->toArray();
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->user)
            ->statePath('data')
            ->schema([
                Grid::make(3)->schema([
                    Group::make()
                        ->schema([
                            Section::make('Foto & Info Akun')
                                ->schema([
                                    FileUpload::make('profile')
                                        ->label('Foto Profil')
                                        ->directory('admins')
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
    
                                    Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'active'   => 'Aktif',
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
                                ->columns(1),
                        ])
                        ->columnSpan(2),
                ]),
            ]);
    }
    

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $this->user->update($data);
        } catch (Halt $exception) {
            return;
        }

        if (request()->hasSession() && isset($this->user->password)) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $this->user->password,
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
