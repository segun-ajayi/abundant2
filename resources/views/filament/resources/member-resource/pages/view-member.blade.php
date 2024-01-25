<x-filament-panels::page
    @class([
        'fi-resource-view-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>

    @php
        $relationManagers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
    @endphp

    @if ((! $hasCombinedRelationManagerTabsWithContent) || (! count($relationManagers)))
        @if ($this->hasInfolist())
            {{ $this->infolist }}
        @else
            <div
                wire:key="{{ $this->getId() }}.forms.{{ $this->getFormStatePath() }}"
            >
                {{ $this->form }}
            </div>
        @endif
    @endif

    @if (count($relationManagers))
        <x-filament-panels::resources.relation-managers
            :active-locale="isset($activeLocale) ? $activeLocale : null"
            :active-manager="$activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
            :content-tab-label="$this->getContentTabLabel()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($hasCombinedRelationManagerTabsWithContent)
                <x-slot name="content">
                    @if ($this->hasInfolist())
                        {{ $this->infolist }}
                    @else
                        {{ $this->form }}
                    @endif
                </x-slot>
            @endif
        </x-filament-panels::resources.relation-managers>
    @endif


    <x-filament::modal icon="heroicon-o-banknotes" id="withdrawSavings">
        <x-slot name="heading">
            Withdraw from Savings
        </x-slot>

        <x-filament-panels::form
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="withDrawSavings"
        >
            <x-filament-forms::field-wrapper.label>Amount</x-filament-forms::field-wrapper.label>
            <x-filament::input.wrapper :valid="! $errors->has('amount')">
                <x-slot name="prefix">
                    {{ config('app.currency') }}
                </x-slot>
                <x-filament::input
                    type="text"
                    wire:model="amount"
                />
            </x-filament::input.wrapper>

            <x-filament-panels::form.actions
                :actions="$this->getAct()"
                :full-width="true"
            />
        </x-filament-panels::form>
    </x-filament::modal>

    <x-filament::modal icon="heroicon-o-banknotes" id="share">
        <x-slot name="heading">
            Withdraw from Shares
        </x-slot>

        <x-filament-panels::form
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="withDrawShares"
        >
            <x-filament-forms::field-wrapper.label>Amount</x-filament-forms::field-wrapper.label>
            <x-filament::input.wrapper :valid="! $errors->has('amount')">
                <x-slot name="prefix">
                    {{ config('app.currency') }}
                </x-slot>
                <x-filament::input
                    type="text"
                    wire:model="amount"
                />
            </x-filament::input.wrapper>

            <x-filament-panels::form.actions
                :actions="$this->getAct()"
                :full-width="true"
            />
        </x-filament-panels::form>
    </x-filament::modal>

    <x-filament::modal icon="heroicon-o-hand-raised" id="loan">
        <x-slot name="heading">
            Modal heading
        </x-slot>

        loan
    </x-filament::modal>

    <x-filament::modal icon="heroicon-o-building-office-2" id="building">
        <x-slot name="heading">
            Modal heading
        </x-slot>

        builging
    </x-filament::modal>
</x-filament-panels::page>
