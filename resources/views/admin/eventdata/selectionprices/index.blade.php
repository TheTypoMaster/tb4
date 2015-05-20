@extends('layouts.master')

@section('main')

<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
        			<h2 class="col-lg-4">Prices</h2>

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
		@if (count($selectionprices))
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
        			<th>Created</th>
        			<th>Updated</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($selectionprices as $selectionprice)
        		<tr>
        			<td>{{ $selectionprice->id }}</td>
        			<td>{{ $selectionprice->selection_name }}</td>
        			<td>{{ $selectionprice->event_name }}</td>
                    <td>{{ $selectionprice->competition_name }}</td>
                    <td>{{ $selectionprice->win_odds }}</td>
                    <td>{{ $selectionprice->place_odds }}</td>
                    <td>{{ $selectionprice->status_name }}</td>
        			<td>{{ $selectionprice->created_at }}</td>
        			<td>{{ $selectionprice->updated_at }}</td>
        			<td>{{ link_to_route('admin.selectionprices.edit', 'Edit', array($selectionprice->id, 'q' => $search), array('class' => 'btn btn-info')) }}</td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {{ $selectionprices->appends(array('q' => $search))->links() }}
        @else
        <p>There are no selections to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop