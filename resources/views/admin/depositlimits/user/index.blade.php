@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            @include('admin.users.partials.header')
            <h4 class="page-header">Deposit Limit {!! link_to_route('admin.users.deposit-limit.edit', "Edit", array($user->id, 'get'), array("class" => "btn btn-warning")) !!}</h4>

            @if( ! $depositLimit )
                <h4>No Deposit Limit</h4>
            @else
                <dl class="dl-horizontal">
                    <dt>Deposit Limit</dt>
                    <dd>${{ number_format($depositLimit->amount, 2) }}</dd>

                    <dt>Notes</dt>
                    <dd>{{ $depositLimit->notes }}</dd>
                </dl>
            @endif

        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop