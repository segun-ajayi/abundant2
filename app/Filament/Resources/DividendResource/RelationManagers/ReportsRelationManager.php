<?php

namespace App\Filament\Resources\DividendResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use stdClass;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

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
            ->columns([
                Tables\Columns\TextColumn::make('#')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                    $livewire->getTablePage() - 1
                                ))
                        );
                    }
                ),
                Tables\Columns\TextColumn::make('member.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->numeric()->prefix(config('app.currency')),
                Tables\Columns\ToggleColumn::make('status')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->member->creditSavings($record->amount, 'dividend', now());
                            $record->dividend->paid++;
                            $record->dividend->unpaid--;
                            $record->dividend->save();
                        } else {
                            $record->member->debitSavings($record->amount, 'dividend reversal', now());
                            $record->dividend->paid--;
                            $record->dividend->unpaid++;
                            $record->dividend->save();
                        }
                    })
                    ->sortable()
                    ->label('Paid'),
                Tables\Columns\TextColumn::make('pDate')
                    ->label('Payment Date')
                    ->date()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->poll();
    }
}
