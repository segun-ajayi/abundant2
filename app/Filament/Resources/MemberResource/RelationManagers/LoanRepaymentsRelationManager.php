<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\On;

class LoanRepaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'loanRepayments';

    public mixed $idx = null;

    #[On('loadRepayments')]
    public function loadRepayments($id = null)
    {
        $this->idx = $id;
        if ($id) {
            return Loan::find($id);
        }
        $ids = $this->ownerRecord->getActiveLoans;

        if ($ids->count() < 1) {
            $ids = $this->ownerRecord->getLastLoan();
        }

        return $ids;
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return count($ownerRecord->loans);
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
        $ids = $this->loadRepayments($this->idx);

//        dd($id);
        if (is_array($ids) && count($ids) === 0) {
            $id = 0;
        } elseif (is_array($ids)) {
            $id = $ids[0];
        } else {
            $id = $ids->first()->id;
        }

        return $table
            ->recordTitleAttribute('credit')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('loan_id', $id))
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
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->before(function (LoanRepayment $record) {
                        $bal = $record->loan->balance;
                        $record->loan->update(
                            [
                                'balance' => $bal + $record->credit
                            ]
                        );
                    })
                    ->after(function (LoanRepayment $record) {
                        $member = Member::find($record->member_id);
                        if ($member) {
                            $record->loan->update([
                                'lpDate' => $member->getLastLoanPay()->date
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
