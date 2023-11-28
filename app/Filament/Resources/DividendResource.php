<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DividendResource\Pages;
use App\Filament\Resources\DividendResource\RelationManagers;
use App\Models\Dividend;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DividendResource extends Resource
{
    protected static ?string $model = Dividend::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $years = [];
        for ($year = Carbon::now()->format('Y'); $year >= 2019; $year--) {
            $years[$year] = $year;
        }
        return $form
            ->schema([
                Forms\Components\Select::make('year')
                    ->options($years)
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('divider', Dividend::getDivider((int) $state))),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('₦'),
                Forms\Components\TextInput::make('divider')
                    ->numeric()
                    ->required()
                    ->prefix('₦'),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()->prefix(config('app.currency'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('divider')
                    ->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('shared')
                    ->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('excess')
                    ->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('paid'),
                Tables\Columns\TextColumn::make('unpaid'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
//                    Tables\Actions\Action::make('process')
//                        ->action(fn () => $this->processDividend()),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (Dividend $record) {
                            foreach ($record->reports as $item) {
                                if ($item->status) {
                                    $item->member->debitSavings($item->amount, 'dividend reversal', now());
                                }
                            }
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Dividend $record) {
                            foreach ($record->reports as $item) {
                                if ($item->status) {
                                    $item->member->debitSavings($item->amount, 'dividend reversal', now());
                                }
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->recordUrl(
                fn (Dividend $record): string => DividendResource::getUrl('view', ['record' => $record->id]),
            );
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReportsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDividends::route('/'),
            'create' => Pages\CreateDividend::route('/create'),
            'view' => Pages\ViewDividend::route('/{record}'),
            'edit' => Pages\EditDividend::route('/{record}/edit'),
        ];
    }
}
