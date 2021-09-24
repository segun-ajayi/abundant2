<div>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><h4>Posting for the month of <span style="font-weight: bolder; margin-right: 50px; color: #1abb6a">{{ $pMonth }}</span></h4></li>
        <li><button class="btn btn-primary btn-sm px-5" wire:click="change">Change</button></li>
    </ol>
    <x-jet-dialog-modal wire:model="isOpen">
        <x-slot name="class">
            {{ __('success') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Change Post Month') }}</h4>
            <button class="close" wire:click="$toggle('isOpen')" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="month">Choose Month</label>
                <div class="col-md-9">
                    <select class="form-control" id="month" wire:model="month">
                        <option value="">Select Month</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <x-jet-input-error for="month" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="year">Choose Year</label>
                <div class="col-md-9">
                    <select class="form-control" id="month" wire:model="year">
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="year" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('isOpen')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="post" wire:loading.attr="loading">
                {{ __('Change') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
