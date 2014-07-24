@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">{{ $title }} Transactions</h2>

		@include('admin::transactions.partials.transaction-list')

	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop