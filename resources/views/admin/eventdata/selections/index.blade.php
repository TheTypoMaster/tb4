@extends('admin.layouts.master')

@section('main')

<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
            <h2 class="col-lg-4">Selections <small>({{ number_format($selections->total()) }})</small></h2>
            <h2 class="col-lg-4 pull-right">
            {!! Form::open(array('method' => 'GET')) !!}
            <div class="input-group custom-search-form col-lg-4 pull-right">
                {!! Form::hidden('market', $market) !!}
                {!! Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search...")) !!}
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
            {!! Form::close() !!}
                </h2>
        </div>
        <div class="pull-right">
            @if($market)
                <div class="pull-right">
                    {!! link_to_route('admin.markets.index', "Back to Markets", array("event" => $event), array("class" => "btn btn-outline btn-warning")) !!}
                </div>
            @endif
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
                    <th>Override Odds</th>
                    <th>Override Type</th>
        			<th>Selection Status</th>
        			<th>Display</th>
                    <th>Team</th>
                    <th>Player</th>
        			<th>Created</th>
        			<th>Updated</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>


        	<tbody>
        		@foreach($selections as $selection)
        		<tr>
        			<td>{{ $selection->id }}</td>
        			<td>{{ $selection->name . ($selection->line ? ' (' . $selection->line . ')' : '' )}}</td>
        			<td>{{ $selection->event_name }}</td>
                    <td>{{ $selection->competition_name }}</td>
                    <td>{{ $selection->win_odds }}</td>
                    <td>{{ $selection->place_odds }}</td>
                    <td>{{ $selection->override_type == 'percentage' ? $selection->override_odds * 100 . '%' : $selection->override_odds }}</td>
                    <td>{{ $selection->override_type }}</td>
                    <td>{{ $selection->status_name }}</td>
        			<td>{{ ($selection->display_flag) ? 'Yes' : 'No' }}</td>
                    <td>{{ object_get($selection->team->first(), 'name', '') }}</td>
                    <td>{{ object_get($selection->player->first(), 'name', '') }}</td>
        			<td>{{ $selection->created_at }}</td>
        			<td>{{ $selection->updated_at }}</td>
        			<td>
                        {!! link_to_route('admin.selections.edit', 'Edit', array($selection->id, "q" => $search), array('class' => 'btn btn-info')) !!}
                        {!! link_to_route('admin.selectionprices.edit', 'Edit Price', array($selection->selection_price_id, 'q' => $search, 'market' => $market, 'event' => $event), array('class' => 'btn btn-warning')) !!}
                    </td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {!! $selections->appends(array('q' => $search, 'market' => $market, 'event' => $event))->render() !!}
        @else
        <p>There are no selections to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop