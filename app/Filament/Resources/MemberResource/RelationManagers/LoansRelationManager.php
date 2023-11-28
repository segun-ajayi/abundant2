<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoansRelationManager extends RelationManager
{
    protected static string $relationship = 'loans';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return count($ownerRecord->getActiveloans);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 1))
            ->columns([
                Tables\Columns\TextColumn::make('approved_on')->date()->label('Date'),
                Tables\Columns\TextColumn::make('amount')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\TextColumn::make('balance')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('lpDate')->date()->label('Last Payment'),
                Tables\Columns\TextColumn::make('referer.name')->label('Referrer 1'),
                Tables\Columns\TextColumn::make('referer2.name')->label('Referrer 2'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
