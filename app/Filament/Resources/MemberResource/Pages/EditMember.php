<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action as ActionAlias;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\RawJs;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('close')
                ->label('Close Account')
                ->hidden(fn (Member $record) => $record->is_closed)
                ->form([
                    TextInput::make('loan')
                        ->default(fn (Member $record) => $record->getLoan() + $record->getAccumulatedInterest())
                        ->disabled()
                        ->live()
                        ->mask(RawJs::make("\$money(\$input)"))
                        ->prefix(config('app.currency'))
                        ->helperText('Loan balance plus accumulated interest')
                        ->hintAction(
                            ActionAlias::make('clear_loan')
                                ->icon('heroicon-m-x-circle')
                                ->button()
                                ->form([
                                    Select::make('mode')
                                        ->options([
                                            'bank' => 'Bank Deposit',
                                            'savings' => 'Savings',
                                            'shares' => 'Shares',
                                            'cash' => 'Cash'
                                        ])
                                        ->default('savings')
                                ])
                                ->action(fn (array $data, Member $record, Set $set) => $this->clearLoan($data, $record, $set))
                                ->color('danger')
                                ->visible(fn (Member $record) => $record->getLoan())
                        ),
                    TextInput::make('savings')
                        ->default(fn (Member $record) => $record->getSaving())
                        ->disabled()
                        ->mask(RawJs::make("\$money(\$input)"))
                        ->prefix(config('app.currency'))
                        ->hintAction(
                            ActionAlias::make('withdraw-savings')
                                ->icon('heroicon-m-banknotes')
                                ->button()
                                ->form([
                                    Select::make('mode')
                                        ->options([
                                            'bank' => 'Bank Deposit',
                                            'cash' => 'Cash'
                                        ])
                                        ->default('bank')
                                ])
                                ->action(fn (array $data, Member $record, Set $set) => $this->clearSaving($data, $record, $set))
                                ->color('danger')
                                ->visible(fn (Member $record) => $record->getSaving() && !$record->getLoan())
                        ),
                    TextInput::make('shares')
                        ->default(fn (Member $record) => $record->getShare())
                        ->disabled()
                        ->mask(RawJs::make("\$money(\$input)"))
                        ->prefix(config('app.currency'))
                        ->hintAction(
                            ActionAlias::make('withdraw-shares')
                                ->icon('heroicon-m-banknotes')
                                ->button()
                                ->form([
                                    Select::make('mode')
                                        ->options([
                                            'bank' => 'Bank Deposit',
                                            'cash' => 'Cash'
                                        ])
                                        ->default('bank')
                                ])
                                ->action(fn (array $data, Member $record, Set $set) => $this->clearShare($data, $record, $set))
                                ->color('danger')
                                ->visible(fn (Member $record) => $record->getShare() && !$record->getLoan())
                        ),
                    Textarea::make('balance')
                        ->extraAttributes(function (Member $record) {
                            $bal = ($record->getShare() + $record->getSaving())
                                    - ($record->getAccumulatedInterest() + $record->getLoan());
                            if ($bal >= 0) {
                                $color = 'green';
                            } else {
                                $color = 'red';
                            }
                            return [
                                'style' => "color: $color; font-size: 2em; text-align: center"
                            ];
                        })
                        ->default(function (Member $record) {
                            return '₦' . number_format(($record->getShare() + $record->getSaving())
                                - ($record->getAccumulatedInterest() + $record->getLoan()), 2, '.', ',');
                        })
                ])
                ->action(fn (array $data, Member $record) => $this->process($data, $record))
                ->requiresConfirmation(),
//            Actions\DeleteAction::make()
//                ->visible(fn (Member $record) => $record->is_closed),
            Actions\RestoreAction::make('restore')
                ->color('info')
                ->after(function (Member $record) {
                    $record->update([
                        'is_closed' => false
                    ]);
                }),
            Actions\ForceDeleteAction::make('force'),
        ];
    }

    private function process(array $data, Member $member)
    {
        if ($member->getSaving() || $member->getShare() || $member->getLoan())
        {
            Notification::make()
                ->title('Kindly balance account before attempting to close it.')
                ->warning()
                ->send();

            return;
        }
        $id = Member::onlyTrashed()->max('member_id') + 1;

        $member->update([
            'member_id' => $id,
            'is_closed' => true,
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        Notification::make()
            ->title('Account closed successfully')
            ->success()
            ->send();
    }

    private function clearLoan(array $data, Member $record, Set $set)
    {
        $interest = $record->getAccumulatedInterest();
        $amount = $record->getLoan() + $interest;
        if ($data['mode'] === 'savings') {
            $savings = $record->getSaving();
            if ($savings > $amount) {
                $record->debitSavings($amount, 'Loan Repayment', now());
                $record->creditLoan($amount, $interest, 'Savings');
            } else {
                $record->debitSavings($savings, 'Loan Repayment', now());
                $record->creditLoan($savings, $interest, 'Savings');
            }
        }elseif ($data['mode'] === 'shares') {
            $savings = $record->getShare();
            if ($savings > $amount) {
                $record->debitShare($amount, 'Loan Repayment', now());
                $record->creditLoan($amount, $interest, 'Savings');
            } else {
                $record->debitShare($savings, 'Loan Repayment', now());
                $record->creditLoan($savings, $interest, 'Savings');
            }
        } else {
            $record->creditLoan($amount, $interest, 'Savings');
        }

        $loan = $record->getLoan() + $record->getAccumulatedInterest();
        $share = $record->getShare();
        $saving = $record->getSaving();

        $bal = '₦' . number_format(($share + $saving) - $loan, 2, '.', ',');

        $set('loan', $loan);
        $set('savings', $saving);
        $set('shares', $share);
        $set('balance', $bal);
    }

    private function clearShare(array $data, Member $record, Set $set)
    {
        $amount = $record->getShare();

        $record->debitShare($amount, 'Account Closing');

        $loan = $record->getLoan() + $record->getAccumulatedInterest();
        $share = $record->getShare();
        $saving = $record->getSaving();

        $bal = '₦' . number_format(($share + $saving) - $loan, 2, '.', ',');

        $set('shares', $share);
        $set('balance', $bal);

        Notification::make()
            ->title('Shares withdrawn successfully')
            ->success()
            ->send();
    }

    private function clearSaving(array $data, Member $record, Set $set)
    {
        $amount = $record->getSaving();

        $record->debitSavings($amount, 'Account Closing', now());

        $loan = $record->getLoan() + $record->getAccumulatedInterest();
        $share = $record->getShare();
        $saving = $record->getSaving();

        $bal = '₦' . number_format(($share + $saving) - $loan, 2, '.', ',');

//        $set('loan', $loan);
        $set('savings', $saving);
//        $set('shares', $share);
        $set('balance', $bal);

        Notification::make()
            ->title('Savings withdrawn successfully')
            ->success()
            ->send();
    }

    protected function beforeValidate(): void
    {
        $this->record->name = $this->record->name . 'KKK' . $this->record->member_id;
        $this->record->member_id = 1010101;
        $this->record->save();
    }

    protected function afterValidate(): void
    {
        $ex = explode('KKK', $this->record->name);
        $this->record->member_id = $ex[1];
        $this->record->name = $ex[0];
        $this->record->save();
    }
}
