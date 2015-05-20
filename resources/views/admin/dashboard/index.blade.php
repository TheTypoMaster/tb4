@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Dashboard</h2>
		@foreach($totals as $total)
		<div class="col-lg-4">
			<div class="panel panel-{{ (isset($total['type'])) ? $total['type'] : 'info' }}">
				<div class="panel-heading">
					<h4>{{ $total['title'] }}</h4>
				</div>
				<div class="panel-body">
					<p>{{ $total['amount'] }}</p>
				</div>
			</div>
		</div>		
		@endforeach
		
		{{--
		<li>Bet Turnover Today</li>
		<li>Bet Payouts Today</li>
		<li>Bet Profit Today</li>
		<li>Number of tournaments Today</li>
		<li>Number of people in tournaments Today</li>
		--}}
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop		