@extends('layouts.app')

@section('css')

@endsection

@section('content')
    <div class="col-sm-12 col-md-12">
        <div class="card">
            <div class="card-header"><i class="fa fa-edit"></i> Utilities
                <div class="card-header-actions">
                    <button class="card-header-action btn btn-warning btn-sm px-5" type="button" data-toggle="modal" data-target="#fineModal">Fine</button>
                    <button class="card-header-action btn btn-primary btn-sm px-5 mr-5" type="button" data-toggle="modal" data-target="#utilModal">Utility</button>
                    <button class="card-header-action btn btn-success btn-sm px-5" type="button" data-toggle="modal" data-target="#successModal">Post</button>
                    <button class="card-header-action btn btn-danger btn-sm px-5" type="button" data-toggle="modal" data-target="#dangerModal">Loan</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="fa fa-align-justify"></i> Savings History</div>
                            <div class="card-body">
                                <table class="table table-responsive-sm table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Utility</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($utilities as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                ₦ {{ number_format($item->amount, 2, '.', ',') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-warning" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Fine</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form  id="fineForm" action="{{ route('fine') }}" method="post">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="hf-email">Savings</label>
                            <div class="col-md-9">
                                <input class="form-control" id="fineAmount" type="number" name="amount" placeholder="Enter Amount" autocomplete="amount">
{{--                                <span class="help-block">Please enter your email</span>--}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="postType">Reason</label>
                            <div class="col-md-9">
                                <select class="form-control" id="reason" name="reason">
                                    <option value="noise">Noise Making</option>
                                    <option value="assault">Abuse & Assault</option>
                                    <option value="late">Lateness</option>
                                    <option value="absent">Absent</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="postType">Payment Method</label>
                            <div class="col-md-9">
                                <select class="form-control" id="payMethod" name="pay">
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Bank Transfer</option>
                                    <option value="savings">Savings</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="member" value="{{ $member->id }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    <button id="postFine" class="btn btn-success" type="button">Post</button>
                </div>
            </div>
            <!-- /.modal-content-->
        </div>
        <!-- /.modal-dialog-->
    </div>

@endsection

@section('script')
    <script>

        $(document).ready(function () {
            $('#select2-2').select2({
                theme: 'bootstrap'
            });
            $('#giveLoan').click(function () {
                swal({
                        title: "Are you sure?",
                        // text: "Your are approving loan ",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, approve loan!",
                        cancelButtonText: "No, cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false },
                    function (isConfirm) {
                        if (isConfirm) {
                            const form = document.querySelector('#loanForm');
                            form.submit();
                        } else {
                            swal("Cancelled", "", "error");
                        }
                    });
            });
            $('#postBTN').click(function () {
                swal({
                        title: "Are you sure?",
                        // text: "Your are approving loan ",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, post!",
                        cancelButtonText: "No, cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false },
                    function (isConfirm) {
                        if (isConfirm) {
                            const form = document.querySelector('#postForm');
                            form.submit();
                        } else {
                            swal("Cancelled", "", "error");
                        }
                    });
            });
            $('#postFine').click(function () {
                swal({
                        title: "Are you sure?",
                        // text: "Your are approving loan ",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, fine!",
                        cancelButtonText: "No, cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false },
                    function (isConfirm) {
                        if (isConfirm) {
                            const form = document.querySelector('#fineForm');
                            form.submit();
                        } else {
                            swal("Cancelled", "", "error");
                        }
                    });
            });
            $('#utilBuy').click(function () {
                swal({
                        title: "Are you sure?",
                        // text: "Your are approving loan ",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, post!",
                        cancelButtonText: "No, cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false },
                    function (isConfirm) {
                        if (isConfirm) {
                            const form = document.querySelector('#utilForm');
                            form.submit();
                        } else {
                            swal("Cancelled", "", "error");
                        }
                    });
            });

        });

    </script>
    @if(Session::has('error'))
        <script>
            toastr.error("{{ Session::get('error') }}", 'Error');
        </script>
    @endif
    <script>
        const loan = document.querySelector('#loan');
        const interest = document.querySelector('#interest');
        const spa = document.querySelector('#spa');
        const balance = "{{ $member->getLoan() }}";
        let last = "{{ $member->getLastPay() }}";
        if (+last === 0) {
            last = 1;
        }
        console.log(last, balance);
        loan.addEventListener('blur', () => {
            // console.log('ok');
            const pay = loan.value;
            const int = (0.01 * balance) * last;
            const loa = pay - int;
            interest.value = int.toFixed(2);
            loan.value = loa.toFixed(2);
        })
    </script>
@endsection
