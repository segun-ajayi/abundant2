@extends('layouts.app')

@section('css')
    <!--suppress ALL -->
    <style>
        table {
            white-space: nowrap;
        }

        #scrollX {
            overflow-x: auto;
        }

        .dataTables_wrapper .dt-buttons {
            float:right;
        }
    </style>
    <link href="{{ asset('vendors/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/Buttons-1.6.1/css/buttons.bootstrap4.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="col-sm-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-edit"></i>
                    Monthly Report
            </div>
            <div class="card-body">
                <div id="scrollX">
                    <table class="table table-striped table-bordered datatable">
                        @if($ret !== 'year')
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>SAVINGS</th>
                                <th>TOTAL SAVINGS</th>
                                <th>SHARES</th>
                                <th>SPECIAL SAVINGS</th>
                                <th>APPROVED LOAN</th>
                                <th>LOAN REPAYMENT</th>
                                <th>LOAN BALANCE</th>
                                <th>LOAN INTEREST</th>
                                <th>UTILITIES</th>
                                <th>FINE</th>
                                <th>BUILDING</th>
                                <th><b>TOTAL</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td>{{ $member->member_id }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>₦{{ number_format($member->savingsM, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->totalSavings, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->shareM, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->specialM, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->appLoan, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->loanRepay, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->loanBalance, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->interest, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->util, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->fines, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->buildingM, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->sum, 2, '.', ',') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Grand Total</th>
                                <th>₦{{ number_format($members->sum('totalSavings'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('shareM'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('savingsM'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('specialM'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('appLoan'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('loanRepay'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('loanBalance'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('interest'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('util'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('fines'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('buildingM'), 2, '.', ',') }}</th>
                                <th><b>₦{{ number_format($members->sum('sum'), 2, '.', ',') }}</b></th>
                            </tr>
                            </tfoot>
                        @else
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>TOTAL SAVINGS</th>
                                <th>TOTAL SHARES</th>
                                <th>TOTAL SPECIAL</th>
                                <th>TOTAL LOAN APP.</th>
                                <th>TOTAL LOAN REPAYMENT</th>
                                <th>TOTAL LOAN INTEREST</th>
                                <th>LOAN BALANCE</th>
                                <th>TOTAL UTILITIES</th>
                                <th>TOTAL FINE</th>
                                <th>TOTAL BUILDING</th>
                                <th><b>TOTAL</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td>{{ $member->member_id }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>₦{{ number_format($member->tSavings, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->tShares, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->tSpecial, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->tLoan, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->tLoanRepayments, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->tLoanInterests, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->loanBalance, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->tUtilities, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->tFines, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->tBuilding, 2, '.', ',') }}</td>
                                    <td>₦{{ number_format($member->sum, 2, '.', ',') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Grand Total</th>
                                <th>₦{{ number_format($members->sum('tSavings'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('tShares'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('tSpecial'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('tLoan'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('tLoanRepayments'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('tLoanInterests'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('loanBalance'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('tUtilities'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('tFines'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('tBuilding'), 2, '.', ',') }}</th>
                                <th>₦{{ number_format($members->sum('sum'), 2, '.', ',') }}</th>
                            </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('vendors/Buttons-1.6.1/js/dataTables.buttons.js') }}"></script>
    <script src="{{ asset('vendors/Buttons-1.6.1/js/buttons.bootstrap4.js') }}"></script>
    <script src="{{ asset('vendors/Buttons-1.6.1/js/jszip.js') }}"></script>
    <script src="{{ asset('vendors/Buttons-1.6.1/js/build/pdfmake.js') }}"></script>
    <script src="{{ asset('vendors/Buttons-1.6.1/js/buttons.flash.js') }}"></script>
    <script src="{{ asset('vendors/Buttons-1.6.1/js/buttons.html5.js') }}"></script>
    <script src="{{ asset('vendors/Buttons-1.6.1/js/buttons.print.js') }}"></script>
    <script src="{{ asset('js/datatables.js') }}"></script>

    @if(Session::has('error'))
        <script>
            toastr.error("{{ Session::get('error') }}", 'Error');
        </script>
    @endif
@endsection
