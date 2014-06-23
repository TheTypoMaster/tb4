@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		@include('admin::users.partials.header')
		<h4 class="page-header">Bets</h4>
		@include('admin::bets.partials.bet-list')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop