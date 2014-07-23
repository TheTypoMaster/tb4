@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Withdrawal Requests <small>{{ $withdrawals->getTotal() }}</small></h2>
		@include('admin::withdrawals.partials.list')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop