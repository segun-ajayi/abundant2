<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Member;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterUser extends Page
{

    use InteractsWithForms;
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.register-user';

    public $name, $email, $password, $passwordConfirmation, $member_id, $role;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('member_id')
                    ->options(
                        Member::query()->pluck('name', 'id')->toArray()
                    )
                    ->searchable()
                    ->native(false)
                    ->label('Member Profile')
                    ->required()
                    ->live()
                    ->autofocus(),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User'
                    ])
                    ->required(),
                TextInput::make('password')
                    ->label(__('filament-panels::pages/auth/register.form.password.label'))
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->same('passwordConfirmation')
                    ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),
                TextInput::make('passwordConfirmation')
                    ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                    ->password()
                    ->required()
                    ->dehydrated(false),

            ])->columns(2);
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    public function getAct(): array
    {
        return [$this->getCreateFormAction()];
    }

    public function create() {
        $data = $this->form->getState();
        $member = Member::find($data['member_id']);

        if (!$member->email) {
            $m = explode(' ', $member->name);
            if (str_starts_with('$m[0', 'Mr')) {
                $email = "$m[1]@abundant";
            } else {
                $email = "$m[0]@abundant";
            }
        } else {
            $email = $member->email;
        }

        $data['name'] = $member->name;
        $data['email'] = $email;

        User::create([
            'name' => $data['name'],
            'role' => $data['role'],
            'email' => $data['email'],
            'password' => $data['password'],
            'member_id' => $data['member_id'],
        ]);

        Notification::make()
            ->title('Exco created successfully!')
            ->success()
            ->send();

        $this->redirect(UserResource::getUrl('index'));
    }
}
