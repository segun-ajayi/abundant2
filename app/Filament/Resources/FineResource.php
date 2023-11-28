<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FineResource\Pages;
use App\Filament\Resources\FineResource\RelationManagers;
use App\Models\Fine;
use App\Models\Member;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Functions';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('member')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Member::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Member::find($value)?->name),
                Select::make('reason')
                    ->options([
                        "noise" => "Noise Making",
                        "assault" => "Abuse & Assault",
                        "late" => "Lateness",
                        "absent" => "Absent",
                        "other" => "Other",
                    ])
                    ->native(false)->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('₦'),
                Select::make('mode')
                    ->options(config('app.paymentMode'))->native(false)->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->label('Member ID'),
                Tables\Columns\TextColumn::make('member.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('credit')
                    ->sortable()
                    ->money('NGN')
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('reason')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('mode')
                    ->badge()
                    ->color(config('app.paymentColor')),
                Tables\Columns\TextColumn::make('enteredBy.name')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('reverse')
                    ->action(fn (Fine $record) => $record->del($record))
                    ->requiresConfirmation()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
//                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListFines::route('/'),
            'create' => Pages\CreateFine::route('/create'),
            'edit' => Pages\EditFine::route('/{record}/edit'),
        ];
    }


}
