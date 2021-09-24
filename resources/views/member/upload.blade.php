@extends('layouts.app')

@section('css')

@endsection

@section('content')
    <div class="col-sm-12 col-md-8">
        <div class="card">
            <div class="card-header">Upload Members</div>
            <form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
                <div class="card-body">
                    @csrf
                    <div class="form-group">
                        <label for="nf-email">Upload Excel File</label>
                        <input class="form-control" id="nf-email" type="file" name="members" >
                        <span class="help-block">Only .xlsx or .csv</span>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-primary" type="submit">
                        <i class="fa fa-dot-circle-o"></i> Submit</button>
                    <button class="btn btn-sm btn-danger" type="reset">
                        <i class="fa fa-ban"></i> Reset</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @if(Session::has('error'))
        <script>
            toastr.error("{{ Session::get('error') }}", 'Error');
        </script>
    @endif
@endsection
