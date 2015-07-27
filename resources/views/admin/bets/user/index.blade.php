@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		@include('admin.users.partials.header')

		@include('admin.bets.partials.bet-list')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop