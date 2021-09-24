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
                    Mark Attendance
            </div>
            <div class="card-body">
                <div id="scrollX">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            @foreach($months as $item)
                                <th>{{ $item }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($members as $item)
                            <tr>
                                <td>{{ $item->member_id }}</td>
                                <td>{{ $item->name }}</td>
                                @foreach($months as $x)
                                    <td>
                                        <label class="switch switch-label switch-success">
                                            <input class="switch-input mark" data-month="{{ $x }}" data-member="{{ $item->id }}" type="checkbox"
                                                @if($item->$x)
                                                    checked
                                                @endif
                                            >
                                            <span class="switch-slider" data-checked="P" data-unchecked="A"></span>
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
{{--                        <tfoot>--}}
{{--                        <tr>--}}
{{--                            <th>#</th>--}}
{{--                            <th>Grand Total</th>--}}
{{--                        </tr>--}}
{{--                        </tfoot>--}}
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
    <script>
        $('.mark').change(function () {
            const id = $(this).data("member");
            const m = $(this).data("month");
            const val = $(this).is(':checked');
            $.post("{{ route('markee') }}", {
                member: id,
                val: val,
                month: m,
                _token: "{{ csrf_token() }}"
            }).then(resp => {
                console.log(resp);
            });
        })
    </script>
    @if(Session::has('error'))
        <script>
            toastr.error("{{ Session::get('error') }}", 'Error');
        </script>
    @endif
@endsection
