@extends('layouts.app')

@section('css')
    <link href="{{ asset('vendors/bootstrap-daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet" />
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
                            <div class="card-header">
                                <i class="icon-note"></i> Monthly Analysis
{{--                                <a class="badge badge-danger" href="https://coreui.io/pro/">CoreUI Pro Component</a>--}}
{{--                                <div class="card-header-actions">--}}
{{--                                    <a class="card-header-action" href="http://www.daterangepicker.com" target="_blank">--}}
{{--                                        <small class="text-muted">docs</small>--}}
{{--                                    </a>--}}
{{--                                </div>--}}
                            </div>
                            <div class="card-body">
                                <form action="{{ route('downloadAnalysis') }}" method="post">
                                    @csrf
                                    <fieldset class="form-group">
                                        <label>Pick Date Range</label>
                                        <div class="input-group">
                                          <span class="input-group-prepend">
                                            <span class="input-group-text">
                                              <i class="fa fa-calendar"></i>
                                            </span>
                                          </span>
                                            <input class="form-control" name="daterange" type="text" />
                                        </div>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label>Type</label>
                                        <div class="input-group">
                                            <select class="form-control" name="item">
                                                <option value="month">Montyly</option>
                                                <option value="year">Yearly</option>
                                            </select>
                                        </div>
                                    </fieldset>
                                    <button type="submit" class="btn btn-primary btn-block">Download</button>
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
    <script src="{{ asset('vendors/moment/js/moment.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap-daterangepicker/js/daterangepicker.js') }}"></script>
    <script>
        $('input[name="daterange"]').daterangepicker({
            opens: 'left',
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });
    </script>
    @if(Session::has('error'))
        <script>
            toastr.error("{{ Session::get('error') }}", 'Error');
        </script>
    @endif
@endsection
