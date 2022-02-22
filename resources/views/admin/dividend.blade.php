@extends('layouts.app')

@section('css')

@endsection

@section('content')
    @livewire('admin.divivend')
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#select2-2').select2({
                theme: 'bootstrap'
            }).on('change', function (e) {
                let livewire = $(this).data('livewire');
                eval(livewire).set('surety', $(this).val());
            });
        });
    </script>
    @if(Session::has('error'))
        <script>
            toastr.error("{{ Session::get('error') }}", 'Error');
        </script>
    @endif
    <script>
        const divs = document.querySelectorAll('.formattedNumberField');

        divs.forEach(el => el.addEventListener('keyup', event => {
            event.target.style.fontWeight = "bold";
            const selection = window.getSelection().toString();
            if ( selection !== '' ) {
                return;
            }
            if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
                return;
            }
            const input = event.target.value.replace(/,/g, '');
            let output = input.replace(/[\D\s\._\-]+/g, "");
            output = output ? parseInt( input, 10 ) : 0;
            output = ( output === 0 ) ? "" : output.toLocaleString( "en-US" );
            event.target.value = output;
        }));
    </script>
@endsection
