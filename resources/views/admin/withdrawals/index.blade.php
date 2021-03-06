@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Withdrawal Requests <small>{{ $withdrawals->total() }}</small>
            <span class="pull-right">
                {!! link_to_route('admin.withdrawals.index', $pending ? "Show All" : "Show Pending", array("pending" => !$pending), array("class"=>"btn btn-info")) !!}
                {!! link_to_route('admin.withdrawal-config.edit', "Email Configuration", array("get"), array("class" => "btn btn-warning")) !!}
            </span>
        </h2>
		@include('admin.withdrawals.partials.list')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop