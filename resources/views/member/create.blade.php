@extends('layouts.app')

@section('css')
{{--    <link href="{{ asset('vendors/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">--}}
@endsection

@section('content')
    @livewire('member.create')
@endsection
