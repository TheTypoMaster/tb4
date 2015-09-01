@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Settings</h2>
		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h4>Global Bet Limits</h4>
				</div>
				<div class="panel-body">
					<div class="form-group">
						{!! Form::model($betLimit, array('method' => 'PUT', 'route' => array('admin.settings.update', 'bet_limit'))) !!}
						{!! Form::label('default_amount', 'Bet Limit Safety Net (in $):') !!}
						{!! Form::text('default_amount', number_format($betLimit->default_amount / 100, 2), array('class' => 'form-control')) !!}
						{!! Form::submit('Update', array('class' => 'btn btn-info')) !!}
						{!! Form::close() !!}
					</div>		
					<div class="form-group">
						{!! Form::model($flexiLimit, array('method' => 'PUT', 'route' => array('admin.settings.update', 'bet_flexi'))) !!}
						{!! Form::label('default_amount', 'Bet Flexi Safety Net (as %):') !!}
						{!! Form::text('default_amount', $flexiLimit->default_amount / 100, array('class' => 'form-control')) !!}
						{!! Form::submit('Update', array('class' => 'btn btn-info')) !!}
						{!! Form::close() !!}
					</div>	
					<div class="form-group">
						{!! Form::model($flexiLimit, array('method' => 'PUT', 'route' => array('admin.settings.update', 'bet_limit_sport'))) !!}
						{!! Form::label('default_amount', 'Sport Bets Limit  Safety Net (as $):') !!}
						{!! Form::text('default_amount', number_format($sportsBetLimit->default_amount / 100, 2), array('class' => 'form-control')) !!}
						{!! Form::submit('Update', array('class' => 'btn btn-info')) !!}
						{!! Form::close() !!}
					</div>						
				</div>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop		