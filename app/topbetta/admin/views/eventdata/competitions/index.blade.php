@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
        			<h2 class="col-lg-4">Competitions <small>({{ number_format($competitions->getTotal()) }})</small>
        			    {{ link_to_route('admin.competitions.create', 'New', null, array('class' => 'btn btn-info')) }}
        			</h2>

        			{{ Form::open(array('method' => 'GET')) }}
        			<div class="input-group custom-search-form col-lg-4 pull-right">
        				{{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search...")) }}
        				<span class="input-group-btn">
        					<button class="btn btn-default" type="button">
        						<i class="fa fa-search"></i>
        					</button>
        				</span>
        			</div>
        			{{ Form::close() }}
        		</div>
		@if (count($competitions))
        <table class="table table-striped table-bordered table-hover">
        	<thead>
        		<tr>
        			<th>ID</th>
        			<th>Competition Name</th>
        			<th>State</th>
        			<th>Events</th>
        			<th>Track</th>
        			<th>Weather</th>
        			<th>Type</th>
        			<th>Grade</th>
        			<th>Country</th>
        			<th>Start Time</th>
        			<th>Close Time</th>
        			<th>Display</th>
        			<th>Created</th>
        			<th>Updated</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($competitions as $competition)
        		<tr>
        			<td>{{ $competition->id }}</td>
        			<td>{{ $competition->name }}</td>
        			<td>{{ $competition->state }}</td>
        			<td>{{ $competition->events }}</td>
        			<td>{{ $competition->track }}</td>
        			<td>{{ $competition->weather }}</td>
        			<td>{{ $competition->type_code }}</td>
        			<td>{{ $competition->grade }}</td>
        			<td>{{ $competition->country }}</td>
        			<td>{{ $competition->start_date }}</td>
        			<td>{{ $competition->close_time }}</td>
        			<td>{{ ($competition->display_flag) ? 'Yes' : 'No' }}</td>
        			<td>{{ $competition->created_at }}</td>
        			<td>{{ $competition->updated_at }}</td>
        			<td>{{ link_to_route('admin.competitions.edit', 'Edit', array($competition->id), array('class' => 'btn btn-info')) }}</td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {{ $competitions->appends(array('q' => $search))->links() }}
        @else
        <p>There are no competitions to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop