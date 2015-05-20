@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Reports</h2>
		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h4>Monthly Tournament Report</h4>
				</div>
				<div class="panel-body">
					{{ Form::open(array('method' => 'GET', 'route' => array('admin.reports.show', 'tournaments'))) }}
					{{ Form::selectMonth('month', date('m'), array('class' => 'form-control')) }}
					{{ Form::selectYear('year', date('Y'), array('class' => 'form-control')) }}
					{{ Form::submit('View', array('class' => 'btn btn-info')) }}
					{{ Form::close() }}
				</div>
			</div>
		</div>
		
		<div class="col-lg-4">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h4>Monthly Bets Report</h4>
				</div>
				<div class="panel-body">
					{{ Form::open(array('method' => 'GET', 'route' => array('admin.reports.show', 'bets'))) }}
					{{ Form::selectMonth('month', date('m'), array('class' => 'form-control')) }}
					{{ Form::selectYear('year', date('Y'), array('class' => 'form-control')) }}
					{{ Form::submit('View', array('class' => 'btn btn-info')) }}
					{{ Form::close() }}
				</div>
			</div>
		</div>
		
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop		