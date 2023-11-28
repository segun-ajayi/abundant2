<x-filament-panels::page>
    @if($this->viewPage === 'form')
        <x-filament-panels::form
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="create"
        >
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getAct()"
                :full-width="true"
            />
        </x-filament-panels::form>

    @else
        {{ $this->table }}
    @endif
</x-filament-panels::page>
