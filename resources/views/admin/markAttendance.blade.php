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
        @livewire('admin.mark-attendance')
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
