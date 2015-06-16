@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		@include('admin::users.partials.header')
		<h2 class="page-header">Tournaments</h2>
		@include('admin::tournaments.user.tournament-list')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop