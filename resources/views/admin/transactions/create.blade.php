@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            @include('admin.users.partials.header')
            <h4 class="page-header">{{ $title }} Transactions</h4>
            @include('admin.transactions.partials.transaction-form')
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop