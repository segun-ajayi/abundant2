<div>
    <div class="row mb-2">
        <div class="col-md-6">
            <button class="btn btn-sm btn-secondary px-5 mr-5 pull-right" wire:click="prev" wire:loading.class="loading">Previous</button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-sm btn-primary px-5 ml-5 inline" wire:click="next" wire:loading.class="loading">Next</button>
            <div class="inline w-50 p-2">
                <button class="btn btn-success btn-sm pull-right">Go</button>
                <input class="form-control pull-right w-50" placeholder="Go to Member" wire:model.lazy="gt" type="number">
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-12">
        <div class="card">
            <div class="card-header"><i class="fa fa-edit"></i> {{ Str::title($member->name) }}
                <div class="card-header-actions">
                    <button class="card-header-action btn btn-warning btn-sm px-5" type="button" wire:click="fineModal">Fine</button>
                    <button class="card-header-action btn btn-primary btn-sm px-5 mr-5" type="button" wire:click="utilModal">Utility</button>
                    <button class="card-header-action btn btn-success btn-sm px-5" type="button" wire:click="postModal">Post</button>
                    @if(!$member->getLoan())
                        <button class="card-header-action btn btn-danger btn-sm px-5" type="button" wire:click="loanModal">Loan</button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-4 mb-5">
                        @if($member->pix)
                            @if(\Illuminate\Support\Facades\Storage::exists('public/member-photos/' . $member->pix))
                                <img class="img-thumbnail rounded" src="{{ asset('storage/member-photos/' . $member->pix) }}" height="350">
                            @else
                                <img class="img-thumbnail rounded" src="{{ asset('storage/member-photos/nopix.png') }}" height="350">
                            @endif
                        @else
                            <img class="img-thumbnail rounded" src="{{ asset('storage/member-photos/nopix.png') }}" height="350">
                        @endif
                    </div>
                    <div class="col-md-6 col-sm-4">
                        Name: <h5>{{ Str::title($member->name) }}</h5>
                        Email: <h5>{{ Str::lower($member->email) }}</h5>
                        Phone: <h5>{{ $member->phone . ' | ' . $member->phone2}}</h5>
                        Profession: <h5>{{ $member->profession}}</h5>
                        Address: <h5>{{ $member->address}}</h5>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <h1 class="text-muted">
                            {{ $member->member_id }}
                        </h1>
                        @if($unPaidDividend)
                            <div class="card text-white bg-cyan">
                                <div class="card-body">
                                    <small class="text-muted">{{ $year }} Unpaid Dividend</small>
                                    <x-jet-input class="form-control formattedNumberField mt-4" wire:model="dividend" />
                                    <x-jet-input-error for="dividend" />

                                    @if(Auth::user()->role == 'admin')
                                        <select class="form-control mt-2" wire:model="mode">
                                            <option value="savings">Savings</option>
                                            <option value="share">Shares</option>
                                            <option value="special">Special</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                        <x-jet-input-error for="mode" />
                                        <button class="btn btn-behance btn-block btn-sm mt-4" wire:click="payDividend">Pay</button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white
                            @if($member->sharePercent() < 50)
                            bg-danger
@elseif($member->sharePercent() < 100)
                            bg-warning
@else
                            bg-success
@endif
                            ">
                            <div class="card-body">
                                <div class="text-value">₦ {{ number_format($member->getShare(), 2, '.', ',') }}</div>
                                <div>Shares</div>
                                <div class="progress progress-xs my-2">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $member->sharePercent() }}%" aria-valuenow="{{ $member->sharePercent() }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Shares.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-behance">
                            <div class="card-body">
                                <div class="text-value">₦ {{ number_format($member->getBuilding(), 2, '.', ',') }}</div>
                                <div>Building</div>
                                {{--                                <small class="text-muted">Building.</small>--}}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card text-white bg-cyan">
                            <div class="card-body">
                                <div class="text-value">₦ {{ number_format($member->getSaving(), 2, '.', ',') }}</div>
                                <small class="text-muted">Savings</small>
                                @if($member->getsSaving() > 0)
                                    <div>Special Savings.</div>
                                    <div class="text-value">₦ {{ number_format($member->getsSaving(), 2, '.', ',') }}</div>
                                @endif
                                @if(Auth::user()->role == 'admin')
                                    <button class="btn btn-behance btn-block btn-sm" wire:click="withdrawModal">Withdraw</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        @if($member->getLoan())
                            <div class="card
                            @if($member->loanPercent() < 50)
                                 text-white bg-danger
                            @elseif($member->loanPercent() < 100)
                                 text-white bg-warning
                            @elseif($member->loanPercent() == 100)
                                 text-white bg-success
                            @endif
                                ">
                                <div class="card-body">
                                    @if($member->getLoanH()->isEmpty() && isset($loan->id))
                                        <div class="btn-group float-right">
                                            <button class="btn btn-sm btn-primary" wire:click="reverseloan" type="button">
                                                Reverse
                                            </button>
                                        </div>
                                    @endif
                                    <div class="text-value">₦ {{ number_format($member->getLoan(), 2, '.', ',') }}</div>
                                    <div>Current Loan</div>
                                    <div>₦ {{ number_format($this->member->getAccumulatedInterest(), 2, '.', ',') }} Acc. Interest</div>
                                    @if($member->getLoan())
                                        <div class="progress progress-xs my-2">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $member->loanPercent() }}%" aria-valuenow="{{ $member->loanPercent() }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">₦ {{ number_format($loan->amount, 2, '.', ',') }} on {{ \Carbon\Carbon::parse($loan->approved_on ?? $loan->created_at)->format('M d, Y') }}.</small><br>
                                        @if($member->getLoanSureties())
                                            <small class="text-muted">Sureties: {{ $member->getLoanSureties() }}.</small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="card">
                                <div class="card-body">

                                    <div>Currently not on Loan</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    @if($member->savings)
                        @livewire('inc.savings-history', ['member' => $member])
                    @endif
                    @if($member->hasActiveLoan())
                        @livewire('inc.loan-history', ['member' => $member])
                    @endif
                    @if($member->share)
                        @livewire('inc.shares-history', ['member' => $member])
                    @endif
                    @if($member->building)
                        @livewire('inc.building-history', ['member' => $member])
                    @endif
                </div>
            </div>
        </div>
        @if(Auth::user()->role == 'admin')
            <div class="col-sm-12 col-md-12">
                        <div class="card">
                            <div class="card-header">
                            Admin Area
                        </div>
                            <div class="card-body">
                            <div class="row">
                                @if($member->user_id)
                                    <div class="col">
                                        <button class="btn btn-danger btn-block" wire:click="removeExco">Remove from Excos</button>
                                    </div>
                                    @if($member->isAdmin())
                                        <div class="col">
                                            <button class="btn btn-warning btn-block" wire:click="revokeAdmin">Revoke Admin</button>
                                        </div>
                                    @else
                                        <div class="col">
                                            <button class="btn btn-secondary btn-block" wire:click="makeAdmin">Make Admin</button>
                                        </div>
                                    @endif
                                @else
                                    <div class="col">
                                        <button class="btn btn-primary btn-block" wire:click="excoModal">Make Exco</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        </div>
                    </div>
        @endif
    </div>
    <x-jet-dialog-modal wire:model="isOpenFine">
        <x-slot name="class">
            {{ __('warning') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Fine') }}</h4>
            <button class="close" type="button" wire:click="$toggle('isOpenFine')" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Savings</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" type="text" wire:model="fineAmount" placeholder="Enter Amount" autocomplete="amount">
                </div>
                <x-jet-input-error for="fineAmount" />
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Reason</label>
                <div class="col-md-9">
                    <select class="form-control" wire:model="fineReason">
                        <option>Choose Reason</option>
                        <option value="noise">Noise Making</option>
                        <option value="assault">Abuse & Assault</option>
                        <option value="late">Lateness</option>
                        <option value="absent">Absent</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <x-jet-input-error for="fineReason" />
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Payment Method</label>
                <div class="col-md-9">
                    <select class="form-control" id="payMethod" wire:model="payMethod">
                        <option>Choose Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="transfer">Bank Transfer</option>
                        <option value="savings">Savings</option>
                    </select>
                </div>
                <x-jet-input-error for="payMethod" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('isOpenFine')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="fine" wire:loading.attr="loading">
                {{ __('Fine') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
    <x-jet-dialog-modal wire:model="isOpenUtil">
        <x-slot name="class">
            {{ __('primary') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Utilities') }}</h4>
            <button class="close" type="button" wire:click="$toggle('isOpenUtil')" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="util">Utilities</label>
                <div class="col-md-9">
                    <select class="form-control" id="util" wire:model="utilityType">
                        <option>Select Type</option>
                        <option value="loan_form">Loan Form</option>
                        <option value="booklet">Booklet</option>
                        <option value="entry_form">Entry Form</option>
                        <option value="chair">Chair/Tent Rental</option>
                    </select>
                    <x-jet-input-error for="utilityType" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="price">Price</label>
                <div class="col-md-9">
                    <input type="text" class="form-control formattedNumberField" id="price" wire:model="price" >
                    <x-jet-input-error for="price" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="postType">Payment Method</label>
                <div class="col-md-9">
                    <select class="form-control" id="payUMethod" wire:model="payMethod">
                        <option>Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="transfer">Bank Transfer</option>
                        <option value="savings">Savings</option>
                    </select>
                    <x-jet-input-error for="payMethod" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('isOpenUtil')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="buyUtil" wire:loading.attr="loading">
                {{ __('Buy') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="isOpenPost">
        <x-slot name="class">
            {{ __('success') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Post') }}</h4>
            <button class="close" type="button" wire:click="$toggle('isOpenPost')" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="hf-email">Savings</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" type="text" wire:model="savings" placeholder="Enter Savings" autocomplete="savings">
                    <x-jet-input-error for="savings" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="hf-email">Loan repay</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" type="text" wire:model.lazy="loanRepay" placeholder="Enter Loan Repayment" autocomplete="loanRepay">
                    <x-jet-input-error for="loanRepay" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="hf-email">Loan Interest</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" type="text" wire:model="loanInterest" placeholder="Enter Loan Interest" autocomplete="loanInterest">
                    <x-jet-input-error for="loanInterest" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="hf-email">Shares</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" type="text" wire:model="shares" placeholder="Enter Shares" autocomplete="shares">
                    <x-jet-input-error for="shares" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="hf-email">Building</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" type="text" wire:model="building" placeholder="Enter Building" autocomplete="building">
                    <x-jet-input-error for="building" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="specialSavings">Special Savings</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" type="text" wire:model="specialSavings" placeholder="Enter Special Savings" autocomplete="specialSavings">
                    <x-jet-input-error for="specialSavings" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="postType">Mode of Payment</label>
                <div class="col-md-9">
                    <select class="form-control" wire:model="payMethod">
                        <option>Select Payment Method</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="savings">Savings</option>
                        @if($member->getsSaving() > 0)
                            <option value="special">Special</option>
                        @endif
                    </select>
                    <x-jet-input-error for="payMethod" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('isOpenPost')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="post" wire:loading.attr="loading">
                {{ __('Post') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="isOpenLoan">
        <x-slot name="class">
            {{ __('danger') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Give Loan') }}</h4>
            <button class="close" type="button" wire:click="$toggle('isOpenLoan')" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="loanType">Type</label>
                <div class="col-md-9">
                    <select class="form-control" id="loanType" wire:model="loanType">
                        <option value="normal" selected>Normal Loan</option>
                        <option value="emergency">Emergency</option>
                    </select>
                    <x-jet-input-error for="loanType" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="duration">Duration</label>
                <div class="col-md-9">
                    <select class="form-control" id="duration" wire:model="duration">
                        <option value="1">1 Month</option>
                        <option value="6">6 Months</option>
                        <option value="12" selected>12 Months</option>
                    </select>
                    <x-jet-input-error for="duration" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="amount">Amount</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" id="amount" type="text" wire:model="amount" placeholder="Enter Amount" autocomplete="amount">
                    <x-jet-input-error for="amount" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Surety</label>
                <x-jet-input id="account_id" type="hidden" wire:model.lazy="surety"/>
                <div wire:ignore class="col-md-9">
                    <select data-livewire="@this" class="form-control select2-multiple" id="select2-2" name="surety[]" multiple="">
                        @foreach($members as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-jet-input-error for="surety" style="margin-left: 8.6rem"/>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="payMethod">Mode of Payment</label>
                <div class="col-md-9">
                    <select class="form-control" id="payMethod" wire:model="payMethod">
                        <option value="bank" selected>Bank Transfer</option>
                        <option value="cash">Cash</option>
                    </select>
                    <x-jet-input-error for="payMethod" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('isOpenLoan')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            @if(!$member->getLoan())
                <x-jet-button class="ml-2" wire:click="giveLoan" wire:loading.attr="loading">
                    {{ __('Post') }}
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="isOpenWithdraw">
        <x-slot name="class">
            {{ __('info') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Withdraw') }}</h4>
            <button class="close" type="button" wire:click="$toggle('isOpenWithdraw')" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="withType">Withdraw from</label>
                <div class="col-md-9">
                    <select class="form-control" wire:model="withdrawFrom">
                        <option value="">Select Account</option>
                        <option value="saving">Savings</option>
                        <option value="shares">Shares</option>
                        @if($member->getsSaving() > 0)
                            <option value="special">Special</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="ammm">Amount</label>
                <div class="col-md-9">
                    <input class="form-control formattedNumberField" id="ammm" type="text" wire:model="amount" placeholder="Enter Amount" autocomplete="amount">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="withMode">Mode of Payment</label>
                <div class="col-md-9">
                    <select class="form-control" id="withMode" wire:model="payMethod">
                        <option value="bank" selected>Bank Transfer</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('isOpenWithdraw')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="withdraw" wire:loading.attr="loading">
                {{ __('Post') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="isOpenExco">
        <x-slot name="class">
            {{ __('warning') }}
        </x-slot>
        <x-slot name="title">
            <h4 class="modal-title">{{ __('Make Exco') }}</h4>
            <button class="close" type="button" wire:click="$toggle('isOpenExco')" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </x-slot>
        <hr class="hr-line-solid">
        <x-slot name="content">
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="hf-email">Username</label>
                <div class="col-md-9">
                    <input class="form-control" type="text" wire:model="username" placeholder="Enter Username" autocomplete="username">
                    <x-jet-input-error for="username" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('isOpenExco')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2" wire:click="exco" wire:loading.attr="loading">
                {{ __('Make Exco') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>









