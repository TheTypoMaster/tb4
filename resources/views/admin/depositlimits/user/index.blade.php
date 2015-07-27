@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            @include('admin.users.partials.header')

            @if( ! $depositLimit )
                <div class="col-lg-4">
                    {!! link_to_route('admin.users.deposit-limit.edit', "Create", array($user->id, 'get'), array("class" => "btn btn-success")) !!}
                </div>
            @else
                <div class="col-lg-4">
                    <dl class="dl-horizontal">
                        <dt>Deposit Limit</dt>
                        <dd>${{ number_format($depositLimit->amount, 2) }}</dd>

                        <dt>Notes</dt>
                        <dd>{{ $depositLimit->notes }}</dd>
                    </dl>
                </div>
                <div class="col-lg-4">
                    {!! link_to_route('admin.users.deposit-limit.edit', "Edit", array($user->id, 'get'), array("class" => "btn btn-warning")) !!}
                </div>
            @endif

        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop