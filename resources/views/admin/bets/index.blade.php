@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Bets</h2>

		@include('admin.bets.partials.bet-list')

	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop