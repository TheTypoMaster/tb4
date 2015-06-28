@if (count($tournaments))

<div class="row col-lg-12">
    {!! Form::open(array("method" => "GET", "class" => "form form-inline")) !!}

        <div class="form-group">
            {!! Form::label("from", 'From') !!}
            {!! Form::datetime("from", $from, array("class" => "datepicker")) !!}
        </div>

        <div class="form-group">
            {!! Form::label("to", "To") !!}
            {!! Form::datetime("to", $to, array("class" => "datepicker")) !!}
        </div>

        <div class="form-group">
            {!! Form::submit("Filter", array("class" => "btn btn-primary")) !!}
        </div>
    {!! Form::close() !!}
</div>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.id', 'ID', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.name', 'Tournament Name', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.tod_flag', 'TOD', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'parent.name', 'Parent Name', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'event_group..name', 'Event Group', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'sport.name', 'Sport', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.start_date', 'Start Date', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.end_date', 'End Date', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>Prize</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.free_credit_flag', 'FCP', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.jackpot_flag', 'Game', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.buy_in', 'Buy In', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.entry_fee', 'Entry Fee', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
			<th>Ent.</th>
			<th>{!! Html::tableFilterLink('admin.tournaments.index', 'tournament.status_flag', 'Status', $order, array('q' => $search, 'to' => $to, 'from' => $from)) !!}</th>
            <th>Action</th>
		</tr>
	</thead>

	<tbody>
		@foreach($tournaments as $tournament)
		<tr>
			<td>{{ $tournament->id }}</td>
			<td>{{ $tournament->name }}</td>
			<td>{{ ($tournament->tod_flag) ? 'Y' : 'N' }}</td>
			<td>{{ (isset($tournament->parentTournament)) ? $tournament->parentTournament->name : '-' }}</td>
			<td>{{ ($tournament->eventGroup) ? $tournament->eventGroup->name : 'n/a' }}</td>
			<td>{{ $tournament->sport->name }}</td>
			<td>{{ $tournament->start_date }}</td>
			<td>{{ $tournament->end_date }}</td>
			<td>{{ (!empty($tournament->jackpot_flag) && $tournament->parent_tournament_id > 0) ? 'Ticket' : 'Cash' }} (${{ number_format($tournament->calculateTournamentPrizePool($tournament->id) / 100, 2) }})</td>
			<td>{{ ($tournament->free_credit_flag) ? 'Y' : 'N' }}</td>
			<td>{{ ($tournament->jackpot_flag) ? 'Jackpot' : 'Single' }}</td>
			<td>{{ ($tournament->buy_in == '0') ? 'free' : '$' . number_format($tournament->buy_in / 100, 2) }}</td>
			<td>{{ ($tournament->entry_fee == '0') ? 'free' : '$' . number_format($tournament->entry_fee / 100, 2) }}</td>
			<td>{{ TopBetta\Models\TournamentTicket::countTournamentEntrants($tournament->id) }}</td>
			<td>{{ ($tournament->status_flag) ? 'Active' : 'Inactive' }}</td>
            <td>
                {!! link_to_route('admin.tournaments.edit', "Edit", array($tournament->id), array("class" => 'btn btn-primary')) !!}
                @if( ! $tournament->cancelled_flag && $tournament->end_date > Carbon\Carbon::now())
                    {!! link_to('/admin/tournaments/add-users/' . $tournament->id, "Add Users", array("class" => "btn btn-info")) !!}
                @endif

                {!! link_to_route('admin.tournaments.show', "Show Entrants", array($tournament->id), array("class" => "btn btn-success")) !!}


                @if( ! $tournament->paid_flag && ! $tournament->cancelled_flag )
                    {!! link_to('/admin/tournaments/cancel/' . $tournament->id, "Cancel", array("class" => "btn btn-warning")) !!}
                @endif

                @if( ! $tournament->tickets->count() )
                    {!! Form::open(array("route" => array("admin.tournaments.destroy", $tournament->id), "method" => "DELETE")) !!}
                    <button type="submit" class="btn btn-danger delete-button">Delete</button>
                    {!! Form::close() !!}
                @endif

            </td>
		</tr>

		@endforeach
	</tbody>
</table>
{!! $tournaments->render() !!}
@else
<p>There are no tournaments to display</p>
@endif

<script type="text/javascript">
    $(".datepicker").datetimepicker({format: 'YYYY-MM-DD HH:mm'});
</script>