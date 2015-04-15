@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Withdrawal Config
            </h2>
            @include('admin::withdrawals.partials.list')
        </div>

@stop