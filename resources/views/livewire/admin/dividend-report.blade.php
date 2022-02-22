<div class="col-sm-12 col-md-12">
    <div class="card">
        <div class="card-header"><i class="fa fa-edit"></i> Dividend Report
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-condensed table-striped">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Year</th>
                                        <th>Total Dividend</th>
                                        <th>Total Shared</th>
                                        <th>Excess Dividend</th>
                                        <th>Total Paid</th>
                                        <th>Total Unpaid</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($dividends as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $item->year }}
                                            </td>
                                            <td>₦ {{ number_format($item->total, 2, '.', ',') }}</td>
                                            <td>₦ {{ number_format($item->shared, 2, '.', ',') }}</td>
                                            <td>₦ {{ number_format($item->excess, 2, '.', ',') }}</td>
                                            <td>{{ $item->paid }}</td>
                                            <td>{{ $item->unpaid }}</td>
                                            <td>
                                                <button wire:click="download('{{ $item->year }}')" class="btn btn-sm btn-primary"><i class="fa fa-download"></i> Download</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="6" class="text-lg font-bold">Couldn't find any result matching you criteria!</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
