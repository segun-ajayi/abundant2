@extends('layouts.app')

@section('css')
{{--    <link href="{{ asset('vendors/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">--}}
@endsection

@section('content')
    <div class="col-sm-12 col-md-12">
        <div class="card">
            <div class="card-header"><i class="fa fa-edit"></i> Edit Member</div>
            <div class="card-body">
                <form action="{{ route('editMember' , $member->id) }}" enctype="multipart/form-data" id="memberForm" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="memberId">Member ID</label>
                        <input type="number" class="form-control" id="memberId" value="{{ $member->member_id }}" name="member_id" placeholder="{{ 'Available IDs: ' . $ava }}">
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $member->name }}">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ $member->address }}">
                    </div>
                    <div class="form-group">
                        <label for="sex">Gender</label>
                        <select class="form-control" id="sex" name="sex">
                            <option value="male" @if($member->sex == 'male') selected @endif>Male</option>
                            <option value="female" @if($member->sex == 'female') selected @endif>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="marital">Marital Status</label>
                        <select class="form-control" id="marital" name="marital">
                            <option value="married" @if($member->marital == 'married') selected @endif>Married</option>
                            <option value="single" @if($member->marital == 'single') selected @endif>Single</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone #</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $member->phone }}">
                    </div>
                    <div class="form-group">
                        <label for="phone2">Phone 2 #</label>
                        <input type="text" class="form-control" id="phone2" name="phone2" value="{{ $member->phone2 }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $member->email }}">
                    </div>
                    <div class="form-group">
                        <label for="profession">Profession</label>
                        <input type="text" class="form-control" id="profession" name="profession" value="{{ $member->profession }}">
                    </div>
                    <div class="form-group">
                        <label for="purpose">Purpose</label>
                        <input type="text" class="form-control" id="purpose" name="purpose" value="{{ $member->purpose }}">
                    </div>
                    <div class="form-group">
                        <label for="referrer">Referrer</label>
                        <select class="form-control" id="referrer" name="referrer">
                            <option value="">Select referrer</option>
                            @foreach($members as $item)
                                <option value="{{ $item->id }}" @if($item->name == $member->referrer) selected @endif >{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nok">Next of Kin</label>
                        <input type="text" class="form-control" id="nok" name="nok" value="{{ $member->nok }}">
                    </div>
                    <div class="form-group">
                        <label for="nok_address">Next of Kin Address</label>
                        <input type="text" class="form-control" id="nok_address" name="nok_address" value="{{ $member->nok_address }}">
                    </div>
                    <div class="form-group">
                        <label for="nok_phone">Next of Kin Phone</label>
                        <input type="text" class="form-control" id="nok_phone" name="nok_phone" value="{{ $member->nok_phone }}">
                    </div>
                    <div class="form-group">
                        <label for="nok_phone2">Next of Kin Phone 2</label>
                        <input type="text" class="form-control" id="nok_phone2" name="nok_phone2" value="{{ $member->nok_phone2 }}">
                    </div>

                    <div class="form-group">
                        <label for="picture">Picture</label>
                        <input type="file" class="form-control" id="picture" name="picture" value="">
                    </div>
                    <input type="hidden" value="{{ $member->id }}" name="id">
                    <button type="button" class="btn btn-primary btn-block" id="submitBTN">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(document).ready(function () {
            $('#select2-2').select2({
                theme: 'bootstrap'
            });
            $('#submitBTN').click(function () {
                swal({
                        title: "Are you sure?",
                        // text: "Your are approving loan ",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, edit member!",
                        cancelButtonText: "No, cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false },
                    function (isConfirm) {
                        if (isConfirm) {
                            const form = document.querySelector('#memberForm');
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
@endsection
