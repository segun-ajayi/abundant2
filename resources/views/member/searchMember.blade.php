@extends('layouts.app')

@section('css')

@endsection

@section('content')
    <div class="col-sm-12 col-md-12">
        <div class="card">
            <div class="card-header"><i class="fa fa-edit"></i> Search Member by Member ID
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('searchMember') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="memberId">Enter Member ID</label>
                                        <input type="number" class="form-control" name="memberId" id="memberId">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
