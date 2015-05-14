@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
        			<h2 class="col-lg-4">Events <small>({{ number_format($events->getTotal()) }})</small></h2>

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
		@if (count($events))
        <table class="table table-striped table-bordered table-hover">
        	<thead>
        		<tr>
        			<th>ID</th>
        			<th>Event Name</th>
					<th>Competition</th>
        			<th>Number</th>
        			<th>Class</th>
        			<th>Distance</th>
        			<th>Event Status</th>
        			<th>Paid</th>
        			<th>Start Time</th>
        			<th>Display</th>
        			<th>Created</th>
        			<th>Updated</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($events as $event)
        		<tr>
        			<td>{{ $event->id }}</td>
        			<td>{{ $event->name }}</td>
        			<td>{{ $event->competition_name }}</td>
        			<td>{{ $event->number }}</td>
        			<td>{{ $event->class }}</td>
        			<td>{{ $event->distance }}</td>
        			<td>{{ $event->event_status_name }}</td>
        			<td>{{ ($event->paid_flag) ? 'Yes' : 'No' }}</td>
        			<td>{{ $event->start_date }}</td>
        			<td>{{ ($event->display_flag) ? 'Yes' : 'No' }}</td>
        			<td>{{ $event->created_at }}</td>
        			<td>{{ $event->updated_at }}</td>
        			<td>
                        {{ link_to_route('admin.events.edit', 'Edit', array($event->id, "q" => $search), array('class' => 'btn btn-info')) }}
                        {{ link_to_route('admin.markets.index', 'Markets', array('event' => $event->id), array('class' => 'btn btn-primary')) }}
                    </td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {{ $events->appends(array('q' => $search))->links() }}
        @else
        <p>There are no events to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop