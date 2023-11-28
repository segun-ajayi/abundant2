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

class LoanRepaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'loanRepayments';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return count($ownerRecord->getActiveloans);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('credit')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        [$id] = $this->ownerRecord->getActiveLoans;

        return $table
            ->recordTitleAttribute('credit')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('loan_id', $id->id))
            ->columns([
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('credit')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('interest')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('mode'),
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
