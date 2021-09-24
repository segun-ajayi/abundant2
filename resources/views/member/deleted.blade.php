@extends('layouts.app')

@section('css')
    <!--suppress ALL -->
    <link href="{{ asset('vendors/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="col-sm-12 col-md-12">
        <div class="card">
            <div class="card-header"><i class="fa fa-edit"></i> Deleted Members</div>
            <div class="card-body">
                <table class="table table-striped table-bordered datatable">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($members as $member)
                        <tr>
                            <td>{{ $member->member_id }}</td>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->phone }}</td>
                            <td>
                                <a class="btn btn-primary btn-sm del" onclick="restore({{ $member->id }})">
                                    <i class="fa fa-recycle"></i>
                                </a>
                                <a class="btn btn-danger btn-sm del" onclick="deletePerm({{ $member->id }})">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script>
        // $('.del').click(function(){
        //    const id = this.val;
        //
        // });
        const restore = (id) => {
            swal({
                    title: "Are you sure?",
                    // text: "Your are approving loan ",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, restore member!",
                    cancelButtonText: "No, cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false },
                function (isConfirm) {
                    if (isConfirm) {
                        $.post('/restore_member/', {
                            id: id,
                            _token: "{{ csrf_token() }}"
                        }).then(data => {
                            swal(data, '', 'success');
                            location.reload();
                        })
                    } else {
                        swal("Cancelled", "", "error");
                    }
                });
        }
        const deletePerm = (id) => {
            swal({
                    title: "Are you sure?",
                    text: "Once deleted, this member canot be recovered",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Permanently delete member!",
                    cancelButtonText: "No, cancel plx!",
                    closeOnConfirm: true,
                    closeOnCancel: false },
                function (isConfirm) {
                    if (isConfirm) {
                        $.post('/perm_member/', {
                            id: id,
                            _token: "{{ csrf_token() }}"
                        }).then(data => {
                            swal(data, '', 'success');
                            location.reload();
                        })
                    } else {
                        swal("Cancelled", "", "error");
                    }
                });
        }
    </script>
    @if(Session::has('error'))
        <script>
            toastr.error("{{ Session::get('error') }}", 'Error');
        </script>
    @endif
@endsection
