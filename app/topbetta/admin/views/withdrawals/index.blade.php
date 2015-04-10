@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Withdrawal Requests <small>{{ $withdrawals->getTotal() }}</small>
            <span class="pull-right">{{ link_to_route('admin.withdrawals.index', $pending ? "Show All" : "Show Pending", array("pending" => !$pending), array("class"=>"btn btn-info")) }}</span>
        </h2>
		@include('admin::withdrawals.partials.list')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop