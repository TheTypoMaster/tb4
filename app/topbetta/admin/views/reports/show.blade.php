@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
			<h2 class="col-lg-8">Show Report: {{{ ucwords($report) }}}
				<small>
					<a href="{{ route('admin.reports.show', array($report)) }}?download=true" class=""><span class="glyphicon glyphicon-download-alt"></span></a>
				</small>
			</h2>
			<span class='pull-right'>{{ link_to_route('admin.reports.index', 'Back to Reports', array(), array('class' => 'btn btn-outline btn-warning')) }}</span>
		</div>
		@if(count($data))
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					@foreach($data[0] as $key => $value)
					<th>{{ $key }}</th>
					@endforeach
				</tr>
			</thead>

			<tbody>
				@foreach($data as $row)
				<tr>
					@foreach($row as $value)
					<td>{{ $value }}</td>
					@endforeach
				</tr>
				@endforeach	
			</tbody>
		</table>	
		{{ $data->appends(array('month' => $month, 'year' => $year))->links() }}
		@else
		<p>No data.</p>
		@endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop		