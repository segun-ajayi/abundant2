<x-filament-panels::page>
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
</x-filament-panels::page>
