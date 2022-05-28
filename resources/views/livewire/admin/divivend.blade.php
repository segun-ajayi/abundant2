<div class="col-sm-12 col-md-12">
    <div class="card">
        <div class="card-header"><i class="fa fa-edit"></i> Post Dividend
            <div class="card-header-actions">
                <button class="card-header-action btn btn-danger btn-sm px-5" type="button" data-toggle="modal" data-target="#dangerModal">Reverse</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="memberId">Total Dividend</label>
                                <input type="text" class="form-control formattedNumberField" wire:model="dividend" id="dividend">
                                <x-jet-input-error for="dividend" />
                            </div>
                            <div class="form-group">
                                <label for="memberId">Total Shares + Total Savings</label>
                                <input type="text" class="form-control formattedNumberField" wire:model="divider" id="divider">
                                <x-jet-input-error for="divider" />
                            </div>
                            <div class="form-group">
                                <label for="year">Year</label>
                                <select class="form-control" wire:model="year" id="year">
                                    <option value="">select year</option>
                                    @for($i = \Carbon\Carbon::now()->format('Y') - 1; $i > (\Carbon\Carbon::now()->format('Y') - 5); $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <x-jet-input-error for="year" />
                            </div>
                            <button wire:click="submit" wire:loading.class="btn-loading" class="btn btn-primary pull-right">Prepare</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-jet-dialog-modal wire:model="isLoading">
        <x-slot name="class">
            {{ __('warning') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Calculating Dividend') }}</h4>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="font-bold text-lg text-red-400">
                Please wait...
            </div>
        </x-slot>
        <x-slot name="footer">
            {{--            <x-jet-secondary-button wire:click="$toggle('isOpenExco')" wire:loading.attr="disabled">--}}
            {{--                {{ __('Nevermind') }}--}}
            {{--            </x-jet-secondary-button>--}}

            {{--            <x-jet-button class="ml-2" wire:click="exco" wire:loading.attr="loading">--}}
            {{--                {{ __('Make Exco') }}--}}
            {{--            </x-jet-button>--}}
        </x-slot>
    </x-jet-dialog-modal>
    <x-jet-dialog-modal wire:model="caution">
        <x-slot name="class">
            {{ __('warning') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Caution!') }}</h4>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="font-bold text-lg text-red-400">
                {{ $year }} dividend already generated, all {{ $year }} dividend records will be overwritten if you choose to continue.
            </div>
        </x-slot>
        <x-slot name="footer">
                        <x-jet-secondary-button wire:click="$toggle('caution')" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </x-jet-secondary-button>

                        <x-jet-button class="ml-2" wire:click="continue" wire:loading.class="btn-loading">
                            {{ __('Continue') }}
                        </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
    <div class="modal fade" id="dangerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-danger" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reverse Dividend</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="loanForm" action="" method="post">
                        @csrf

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="duration">Duration</label>
                            <div class="col-md-9">
                                <select class="form-control" name="year" id="year3">
                                    <option>select year</option>
                                    @for($i = \Carbon\Carbon::now()->format('Y') - 1; $i > (\Carbon\Carbon::now()->format('Y') - 5); $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="giveLoan" type="button">Reverse</button>
                </div>
            </div>
            <!-- /.modal-content-->
        </div>
        <!-- /.modal-dialog-->
    </div>
</div>
