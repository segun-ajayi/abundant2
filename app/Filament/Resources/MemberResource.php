<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Filament\Resources\MemberResource\Widgets\LoanOverview;
use App\Filament\Resources\MemberResource\Widgets\SavingsOverview;
use App\Filament\Resources\MemberResource\Widgets\ShareOverview;
use App\Filament\Resources\MemberResource\Widgets\SharesOverview;
use App\Filament\Resources\MemberResource\Widgets\SpecialSavingsOverview;
use App\Models\Member;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Filament\Infolists\Components;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Functions';


    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Personal Info')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\TextInput::make('member_id')
                                    ->required()
                                    ->numeric()
                                    ->placeholder(function () {
                                        $aa = '';
                                        $av = DB::select('select distinct member_id+1 as s from members where member_id + 1 not in(select distinct member_id from members)');
//                                                dd($av);
                                        foreach ($av as $a) {
                                            if (!$aa) {
                                                $aa = $a->s;
                                                continue;
                                            }
                                            $aa = $aa . ', ' . $a->s;
                                        }
                                        return $aa;
                                    })
                                    ->unique('members', 'member_id')
                                    ->label('Member ID'),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('address')
                                    ->columnSpan(3)
                                    ->required(),
                                Forms\Components\Select::make('sex')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female'
                                    ])
                                    ->required()
                                    ->label('Gender'),
                                Forms\Components\Select::make('marital')
                                    ->options([
                                        'married' => 'Married',
                                        'single' => 'Single',
                                        'single parent' => 'Single Parent',
                                        'widowed' => 'Widowed'
                                    ])
                                    ->required()
                                    ->label('Marital Status'),
                                Forms\Components\TextInput::make('phone')
                                    ->required()
                                    ->label('Phone #'),
                                Forms\Components\TextInput::make('phone2')
                                    ->label('Phone 2 #'),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('profession')
                                    ->required(),
                                Forms\Components\TextInput::make('purpose')
                                    ->required()
                                    ->columnSpan(2),
                                Select::make('referrer')
                                    ->relationship('referer', 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->columnSpanFull()
                            ])->columns(3)
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Next of Kin')
                            ->schema([
                                Select::make('nok')
                                    ->options(Member::all()->pluck('name')->toArray())
                                    ->native(false)
                                    ->searchable()
                                    ->label('Next of Kin')
                                    ->columnSpanFull()
                                    ->required(),
                                Forms\Components\TextInput::make('nok_address')
                                    ->label('Next of Kin Address')
                                    ->columnSpanFull()
                                    ->required(),
                                Forms\Components\TextInput::make('nok_phone')
                                    ->label('Phone #')
                                    ->required(),
                                Forms\Components\TextInput::make('nok_phone2')
                                    ->label('Phone 2 #')
                            ])->columns(2),
                        Forms\Components\Section::make('Member Picture')
                            ->schema([
                                Forms\Components\FileUpload::make('pix')
                                    ->image()
                                    ->hiddenLabel()
                                    ->directory('member-photos')
                                    ->imageEditor()
                            ])
                    ])
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('pix')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(function ($state) {
                        if (file_exists($state)) {
//                            return $state;
                            return 'http://127.0.0.1:8000/storage/member-photos/108.png';
                        } else {
                            return 'http://127.0.0.1:8000/storage/member-photos/nopix.png';
                        }
                    }),
                Tables\Columns\TextColumn::make('member_id')
                    ->sortable()
                    ->searchable()
                    ->label('Member ID'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone Number'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn (Member $record): bool => \Auth::user()->can('update', $record)),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (Member $record) => \Auth::user()->can('delete', $record)),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\ImageEntry::make('pix')
                                ->hiddenLabel()
                                ->height(250)
                                ->grow(false),
                            Components\Grid::make(2)
                                ->schema([
                                    Components\Group::make([
                                        Components\TextEntry::make('name'),
                                        Components\TextEntry::make('profession'),
                                        Components\TextEntry::make('address'),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('member_id')->label('Member ID'),
                                        Components\TextEntry::make('referrer'),
                                    ])
                                ])

                        ])->from('lg'),
                        Components\Fieldset::make('Post')->schema([
                            Components\Actions::make([
                                Action::make('fine')
                                    ->button()
                                    ->color('danger')
                                    ->form([
                                        Forms\Components\TextInput::make('amount')
                                            ->numeric()
                                            ->required()
                                            ->prefix('₦'),
                                        Select::make('mode')
                                            ->label('Payment Mode')
                                            ->options([
                                                "cash" => "Cash",
                                                "savings" => "Savings",
                                                "bank" => "Bank Deposit",
                                                "transfer" => "Bank Transfer",
                                            ])
                                            ->required()
                                            ->native(false),
                                        Select::make('reason')
                                            ->label('Select Reason')
                                            ->options([
                                                "noise" => "Noise Making",
                                                "assault" => "Abuse & Assault",
                                                "late" => "Lateness",
                                                "absent" => "Absent",
                                                "other" => "Other",
                                            ])
                                            ->required()
                                            ->native(false),
                                    ])
                                    ->action(function ($data, Member $record) {
                                        $record->postFine($data);
                                        Notification::make()
                                            ->title("$record->name was fined successfully!")
                                            ->success()
                                            ->send();
                                    }),

                                Action::make('utility')
                                    ->button()
                                    ->color('info')
                                    ->form([
                                        Select::make('type')
                                            ->label('Type of utility')
                                            ->options([
                                                "loanForm" => "Loan Foam",
                                                "entryForm" => "Entry Form",
                                                "booklet" => "Booklet",
                                                "chair" => "Table/Chair Rental",
                                                "others" => "Others",
                                            ])
                                            ->required()
                                            ->native(false),

                                        Forms\Components\TextInput::make('amount')
                                            ->numeric()
                                            ->required()
                                            ->prefix('₦'),

                                        Select::make('mode')
                                            ->label('Payment Mode')
                                            ->options([
                                                "cash" => "Cash",
                                                "savings" => "Savings",
                                                "bank" => "Bank Deposit",
                                            ])
                                            ->required()
                                            ->native(false),
                                    ])
                                    ->action(function ($data, Member $record) {
                                        $record->buyUtil($data);
                                        Notification::make()
                                            ->title("Utility bought successfully!")
                                            ->success()
                                            ->send();
                                    }),

                                Action::make('post')
                                    ->icon('heroicon-m-banknotes')
                                    ->button()
                                    ->color('success')
                                    ->form([
                                        Forms\Components\TextInput::make('savings')
                                            ->mask(RawJs::make("\$money(\$input)"))
                                            ->prefix(config('app.currency')),
                                        Forms\Components\TextInput::make('loan')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, $state, Member $record) {
                                                $accu = $record->getAccumulatedInterest();
                                                $old = (int)str_replace(',', '', $state);
                                                $set('interest', number_format($accu, 2));
                                                $set('loan', number_format($old - $accu, 2));
                                            })
                                            ->mask(RawJs::make("\$money(\$input)"))
                                            ->prefix(config('app.currency')),
                                        Forms\Components\TextInput::make('interest'),
//                                            ->currencyMask(',', '.', 2),
                                        Forms\Components\TextInput::make('shares')
                                            ->mask(RawJs::make("\$money(\$input)"))
                                            ->prefix(config('app.currency')),
                                        Forms\Components\TextInput::make('building')
                                            ->mask(RawJs::make("\$money(\$input)"))
                                            ->prefix(config('app.currency')),
                                        Forms\Components\TextInput::make('special')
                                            ->mask(RawJs::make("\$money(\$input)"))
                                            ->prefix(config('app.currency')),
                                        Select::make('mode')
                                            ->label('Payment Mode')
                                            ->options(config('app.paymentMode'))
                                            ->required()
                                            ->default('bank')
                                            ->native(false),
                                    ])
                                    ->action(function ($data, Member $record) {
                                        $record->post($data);
                                    }),
                                Action::make('loan')
                                    ->button()
                                    ->color('primary')
                                    ->hidden(fn(Member $record) => $record->getLoan())
                                    ->form([
                                        Forms\Components\TextInput::make('amount')
//                                            ->mask(RawJs::make("\$money(\$input)"))
                                            ->prefix(config('app.currency'))
                                            ->numeric()
                                            ->maxValue(fn (Member $record) => $record->getSaving() * 3),
                                        Forms\Components\Select::make('duration')
                                            ->options([
                                                '1' => '1 Month',
                                                '6' => '6 Months',
                                                '12' => '12 Months',
                                            ])
                                            ->required()->default('12')->native(false),
                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'normal' => 'Normal',
                                                'emergency' => 'Emergency'
                                            ])
                                            ->required()->default('normal')->native(false),
                                        Forms\Components\Select::make('surety')
                                            ->multiple()
                                            ->searchable()
                                            ->minItems(2)
                                            ->maxItems(3)
                                            ->getSearchResultsUsing(fn (string $search): array => Member::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                                            ->getOptionLabelUsing(fn ($value): ?string => Member::find($value)?->name),
                                        Forms\Components\Select::make('mode')
                                            ->options(config('app.paymentMode'))
                                            ->required()->default('bank')->native(false),
                                    ])
                                    ->action(function ($data, Member $record) {
                                        $record->takeLoan($data);
                                    })

                            ])->columns(1)->columnSpanFull()->fullWidth()->alignment(Alignment::Center),
                        ])->columnSpanFull(),
                    ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            RelationManagers\SavingsRelationManager::class,
            RelationManagers\ShareRecordRelationManager::class,
            RelationManagers\BuildingRelationManager::class,
            RelationGroup::make('loan', [
                RelationManagers\LoansRelationManager::class,
                RelationManagers\LoanRepaymentsRelationManager::class,
            ])
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            SharesOverview::class,
            SavingsOverview::class,
            SpecialSavingsOverview::class,
            LoanOverview::class
        ];
    }
}
