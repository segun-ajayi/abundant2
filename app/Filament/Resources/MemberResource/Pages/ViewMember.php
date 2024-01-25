<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewMember extends ViewRecord
{
    public $amount;

    protected static string $resource = MemberResource::class;

    protected static string $view = 'filament.resources.member-resource.pages.view-member';

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MemberResource\Widgets\SharesOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }

    protected function getHeaderActions(): array
    {
        $id = $this->record->member_id;
        $prev = Member::where('member_id', '<', $id)
            ->orderBy('member_id', 'desc')->limit(1)
            ->get();
        $next = Member::where('member_id', '>', $id)
            ->orderBy('member_id', 'asc')->take(1)
            ->get();

        if (!isset($prev[0])) {
            $ret = [
                Action::make('next')
                    ->color('success')
                    ->hidden(fn () => $next->isEmpty())
                    ->url('/admin/members/' . $next[0]?->id),
            ];
        } elseif (!isset($next[0])) {
            $ret = [
                Action::make('previous')
                    ->color('danger')
                    ->hidden(fn () => $prev->isEmpty())
                    ->url('/admin/members/' . $prev[0]->id),
            ];
        } else {
            $ret = [

                Action::make('previous')
                    ->color('danger')
                    ->hidden(fn () => $prev->isEmpty())
                    ->url('/admin/members/' . $prev[0]->id),

                Action::make('next')
                    ->color('success')
                    ->hidden(fn () => $next->isEmpty())
                    ->url('/admin/members/' . $next[0]?->id),

//            Action::make('work')
//                ->color('success')
//                ->action(fn () => $this->record->workSavings()),
            ];
        }
        return $ret;
    }

    protected function getCreateFormAction(): array
    {
        return [
            Action::make('withdraw')
                ->label('Withdraw')
                ->requiresConfirmation()
                ->submit('withDrawSavings')
        ];
    }

    public function getAct() {
        return $this->getCreateFormAction();
    }

    public function withDrawSavings() {

        $this->validate($this->rules());
        $this->record->debitSavings($this->amount, 'withdrawal');

        $this->dispatch('close-modal', id: 'withdrawSavings');

        $this->reset('amount');

        Notification::make()
            ->title('Withdrawal successful!')
            ->success()
            ->send();
    }

    public function withDrawShares() {

        $this->validate($this->rules());
        $this->record->debitShare($this->amount, 'withdrawal');

        $this->dispatch('close-modal', id: 'share');

        $this->reset('amount');

        Notification::make()
            ->title('Withdrawal successful!')
            ->success()
            ->send();
    }

    private function rules() : array
    {
        return [
            'amount' => 'required'
        ];
    }

}
