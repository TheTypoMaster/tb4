@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		@include('admin.users.partials.header')
		<h4>Edit Bet Limit</h4>	
		<div class='col-lg-6'>
			{!! Form::model($betLimit, array('method' => 'PATCH', 'route' => array('admin.users.bet-limits.update', $user->id, $betLimit->id))) !!}
			<div class="form-group">
				{!! Form::label('bet_limit_type_id', 'Bet Limit Type:') !!}
				{!! Form::hidden('bet_limit_type_id', $betLimit->bet_limit_type_id) !!}
				<p class="form-control-static"><i>{{ $betLimitTypes[$betLimit->bet_limit_type_id] }}</i></p>
			</div>				
			<div class="form-group">
				{!! Form::label('amount', ($betLimit->limitType->name != 'bet_flexi') ? 'Amount (as $)'  : 'Amount (as %)') !!}
				{!! Form::text('amount', null, array('class' => 'form-control', 'placeholder' => 'e.g. 75.00')) !!}
			</div>										
			<div class="form-group">
				{!! Form::label('notes', 'Notes:') !!}
				{!! Form::textarea('notes', null, array('class' => 'form-control', 'placeholder' => 'Notes')) !!}
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
				{!! Form::submit('Update', array('class' => 'btn btn-info')) !!}
				{!! link_to_route('admin.users.bet-limits.index', 'Cancel', array($user->id), array('class' => 'btn btn-warning')) !!}
			</div>						
			{!! Form::close() !!}
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop