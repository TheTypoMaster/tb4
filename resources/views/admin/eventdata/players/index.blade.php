@extends('admin.layouts.master')

@section('main')
    @include('admin.eventdata.partials.templates.index-template', $data);
@stop