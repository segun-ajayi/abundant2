<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use App\Models\BuildingHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BuildingRelationManager extends RelationManager
{
    protected static string $relationship = 'buildingHistory';


    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('balance')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('balance')
            ->columns([
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('credit')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('debit')->numeric()->prefix(config('app.currency')),
//                Tables\Columns\TextColumn::make('balance')->numeric()->prefix(config('app.currency')),
                Tables\Columns\TextColumn::make('mode')->badge()
                    ->color(config('app.paymentColor')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (BuildingHistory $record) {
                        $cre = $record->credit;
                        $deb = $record->debit;
                        $building = $record->building;
                        $bal = $building->balance;
                        if ($cre) {
                            $building->update([
                                'balance' => $bal - $cre
                            ]);
                        } else {
                            $building->update([
                                'balance' => $bal + $deb
                            ]);
                        }
                    }),
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
