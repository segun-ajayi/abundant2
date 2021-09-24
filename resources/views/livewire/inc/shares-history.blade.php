<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Share History</div>
        <div class="card-body">
            <table class="table table-responsive-sm table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Type</th>
                    @if(Auth::user()->role == 'admin')
                        <th>
                            Action
                        </th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($member->getShareH()->sortByDesc('date') as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}</td>
                        <td>
                            @if($item->credit != 0)
                                ₦ {{ number_format($item->credit, 2, '.', ',') }}
                            @else
                                ₦ {{ number_format($item->debit, 2, '.', ',') }}
                            @endif
                        </td>
                        <td>
                            @if($item->credit != 0)
                                <span class="badge badge-success">{{ $item->mode }}</span>
                            @else
                                <span class="badge badge-danger">{{ $item->mode }}</span>
                            @endif
                        </td>
                        <td>
                            @if($item->credit != 0)
                                <span class="badge badge-success">Credit</span>
                            @else
                                <span class="badge badge-danger">Debit</span>
                            @endif
                        </td>
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
