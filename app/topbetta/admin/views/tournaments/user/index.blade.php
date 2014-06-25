@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		@include('admin::users.partials.header')
		<h4 class="page-header">Tournaments</h4>
		@include('admin::tournaments.partials.tournament-list')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop