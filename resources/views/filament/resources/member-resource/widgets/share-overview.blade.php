<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <div class="card
            @if($record->sharePercent() < 50)
                ring-danger-600
            @elseif($record->sharePercent() < 100)
                ring-danger-600
            @else
                ring-danger-600
            @endif
        ">
        <div class="card-body">
            <div class="text-value font-extrabold">₦ {{ number_format($record->getShare(), 2, '.', ',') }}</div>
            <div>Shares</div>
            <div class="progress progress-xs my-2">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $record->sharePercent() }}%" aria-valuenow="{{ $record->sharePercent() }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="text-muted">Shares.</small>
        </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
