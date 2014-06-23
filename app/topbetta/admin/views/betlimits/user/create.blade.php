@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		@include('admin::users.partials.header')
		<h4>Add Bet Limit</h4>	
		<div class='col-lg-6'>
			{{ Form::open(array('route' => array('admin.users.bet-limits.store', $user->id))) }}
			<div class="form-group">
				{{ Form::label('bet_limit_type_id', 'Bet Limit Type:') }}
				{{-- Form::text('bet_limit_type_id', null, array('class' => 'form-control', 'placeholder' => 'Bet Limit Type')) --}}
				{{ Form::select('bet_limit_type_id', $betLimitTypes, null, array('class' => 'form-control')) }}
			</div>				
			<div class="form-group">
				{{ Form::label('amount', 'Amount (in $ or as %):') }}
				{{ Form::text('amount', null, array('class' => 'form-control', 'placeholder' => 'e.g. 75.00')) }}
			</div>										
			<div class="form-group">
				{{ Form::label('notes', 'Notes:') }}
				{{ Form::textarea('notes', null, array('class' => 'form-control', 'placeholder' => 'Notes')) }}
			</div>				
		</div>
		
		@if ($errors->any())
		<div class="alert alert-danger col-lg-6">
			<ul>
				{{ implode('', $errors->all('<li class="error">:message</li>')) }}
			</ul>
		</div>	
		@endif		
		
		<div class="col-lg-12">
			<div class="form-group">
				{{ Form::hidden('user_id', $user->id) }}
				{{ Form::submit('Add', array('class' => 'btn btn-info')) }}
				{{ link_to_route('admin.users.bet-limits.index', 'Cancel', array($user->id), array('class' => 'btn btn-warning')) }}
			</div>						
			{{ Form::close() }}
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop