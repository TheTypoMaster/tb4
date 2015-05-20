@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Create Bet Limit</h2>

		{!! Form::open(array('route' => 'admin.bet-limits.store')) }}
		<div class='col-lg-6'>
			<div class="form-group">
				{!! Form::label('name', 'Name:') }}
				{!! Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) }}
			</div>				
			<div class="form-group">
				{!! Form::label('value', 'Value:') }}
				{!! Form::text('value', null, array('class' => 'form-control', 'placeholder' => 'Value')) }}
			</div>				
			<div class="form-group">
				{!! Form::label('default_amount', 'Default Amount:') }}
				{!! Form::text('default_amount', null, array('class' => 'form-control', 'placeholder' => 'Default Amount')) }}
			</div>				
			<div class="form-group">
				{!! Form::label('notes', 'Notes:') }}
				{!! Form::textarea('notes', null, array('class' => 'form-control', 'placeholder' => 'Notes')) }}
			</div>				
		</div>	
		<div class="col-lg-12">
			<div class="form-group">
				{!! Form::submit('Create', array('class' => 'btn btn-info')) }}
				{!! link_to_route('admin.bet-limits.index', 'Cancel', array(), array('class' => 'btn btn-outline btn-warning')) }}
			</div>						
			{!! Form::close() }}
		</div>

		@if ($errors->any())
		<ul>
			{{ implode('', $errors->all('<li class="error">:message</li>')) }}
		</ul>
		@endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop