<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UtilityResource\Pages;
use App\Filament\Resources\UtilityResource\RelationManagers;
use App\Models\Member;
use App\Models\Utility;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UtilityResource extends Resource
{
    protected static ?string $model = Utility::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationGroup = 'Functions';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('member_id')
                    ->required()
                    ->relationship('member', 'name')
                    ->searchable(['name', 'member_id']),
                Select::make('name')
                    ->required()
                    ->options([
                        "loanForm" => "Loan Foam",
                        "entryForm" => "Entry Form",
                        "booklet" => "Booklet",
                        "chair" => "Table/Chair Rental",
                        "others" => "Others",
                    ])
                    ->searchable()->label('Utility'),
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
                Tables\Columns\TextColumn::make('member_id')
                    ->sortable()
                    ->searchable()
                    ->label('Member ID'),
                Tables\Columns\TextColumn::make('member.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->money('NGN')
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color('success')
                    ->label('Utility'),
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
                Tables\Actions\EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUtilities::route('/'),
            'create' => Pages\CreateUtility::route('/create'),
            'edit' => Pages\EditUtility::route('/{record}/edit'),
        ];
    }
}
