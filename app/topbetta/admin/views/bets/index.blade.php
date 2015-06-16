@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Bets</h2>
            </div>
            @include('admin::bets.partials.bet-list')
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop