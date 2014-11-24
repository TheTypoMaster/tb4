@extends('layouts.master')

@section('main')

<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
        			<h2 class="col-lg-4">Selections</h2>

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
		@if (count($selections))
        <table class="table table-striped table-bordered table-hover">
        	<thead>
        		<tr>
        			<th>ID</th>
        			<th>Selection</th>
                    <th>Event</th>
                    <th>Competition</th>
                    <th>Win Odds</th>
                    <th>Place Odds</th>
        			<th>Selection Status</th>
        			<th>Display</th>
        			<th>Created</th>
        			<th>Updated</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($selections as $selection)
        		<tr>
        			<td>{{ $selection->id }}</td>
        			<td>{{ $selection->name }}</td>
        			<td>{{ $selection->event_name }}</td>
                    <td>{{ $selection->competition_name }}</td>
                    <td>{{ $selection->win_odds }}</td>
                    <td>{{ $selection->place_odds }}</td>
                    <td>{{ $selection->status_name }}</td>
        			<td>{{ ($selection->display_flag) ? 'Yes' : 'No' }}</td>
        			<td>{{ $selection->created_at }}</td>
        			<td>{{ $selection->updated_at }}</td>
        			<td>{{ link_to_route('admin.selections.edit', 'Edit', array($selection->id), array('class' => 'btn btn-info')) }}</td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {{ $selections->appends(array('q' => $search))->links() }}
        @else
        <p>There are no selections to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop