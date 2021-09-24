<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Loan Repayment History</div>
        <div class="card-body">
            <table class="table table-responsive-sm table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Interest</th>
                    {{--                                                <th>Mode</th>--}}
                    @if(Auth::user()->role == 'admin')
                        <th>
                            Action
                        </th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($member->getLoanH()->sortByDesc('date') as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}</td>
                        <td>₦ {{ number_format($item->credit, 2, '.', ',') }}</td>
                        <td>₦ {{ number_format($item->interest, 2, '.', ',') }}</td>
                        {{--                                                    <td>--}}
                        {{--                                                        <span class="badge badge-success">{{ $item->mode }}</span>--}}
                        {{--                                                    </td>--}}
                        @if(Auth::user()->role == 'admin')
                            <td>
                                <button class="btn btn-primary btn-sm" wire:click="reverse({{ $item }})">Reverse</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
