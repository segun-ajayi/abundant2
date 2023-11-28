<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShareRecordRelationManager extends RelationManager
{
    protected static string $relationship = 'shareRecord';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('date')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('credit')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('debit')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('balance')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('mode')->badge()
                    ->color(config('app.paymentColor')),
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
            ])
            ->defaultSort('date', 'desc');
    }


}
