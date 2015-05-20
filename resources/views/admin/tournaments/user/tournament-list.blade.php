@if (count($tournamentInfo))
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Tournament Name</th>
			<th>TOD</th>
			<th>Parent Name</th>
			<th>Event Group</th>
			<th>Type/Sport</th>
			<th>Start Time</th>
			<th>End Time</th>
			<th>Prize</th>
			<th>FCP</th>
			<th>Game</th>
			<th>Buy-in</th>
			<th>Entry-fee</th>
			<th>Ent.</th>
			<th>Status</th>
            <th>Position</th>
            <th>Cash Prize</th>
            <th>Bonus Credit Prize</th>
		</tr>
	</thead>

	<tbody>
		@foreach($tournamentInfo as $tournament)
		<tr>
			<td>{{ $tournament['tournament']->id }}</td>
			<td>{{ $tournament['tournament']->name }}</td>
			<td>{{ ($tournament['tournament']->tod_flag) ? 'Y' : 'N' }}</td>
			<td>{{ (isset($tournament['tournament']->parentTournament)) ? $tournament['tournament']->parentTournament->name : '-' }}</td>
			<td>{{ ($tournament['tournament']->eventGroup) ? $tournament['tournament']->eventGroup->name : 'n/a' }}</td>
			<td>{{ $tournament['tournament']->sport->name }}</td>
			<td>{{ $tournament['tournament']->start_date }}</td>
			<td>{{ $tournament['tournament']->end_date }}</td>
			<td>{{ (!empty($tournament['tournament']->jackpot_flag) && $tournament['tournament']->parent_tournament_id > 0) ? 'Ticket' : 'Cash' }} (${{ number_format($tournament['tournament']->calculateTournamentPrizePool($tournament['tournament']->id) / 100, 2) }})</td>
			<td>{{ ($tournament['tournament']->free_credit_flag) ? 'Y' : 'N' }}</td>
			<td>{{ ($tournament['tournament']->jackpot_flag) ? 'Jackpot' : 'Single' }}</td>
			<td>{{ ($tournament['tournament']->buy_in == '0') ? 'free' : '$' . number_format($tournament['tournament']->buy_in / 100, 2) }}</td>
			<td>{{ ($tournament['tournament']->entry_fee == '0') ? 'free' : '$' . number_format($tournament['tournament']->entry_fee / 100, 2) }}</td>
			<td>{{ TopBetta\Models\TournamentTicket::countTournamentEntrants($tournament['tournament']->id) }}</td>
			<td>{{ ($tournament['tournament']->status_flag) ? 'Active' : 'Inactive' }}</td>
            <td>{{ $tournament['position'] ? : "N/A" }}</td>
            <td>${{ number_format($tournament['prize']/100, 2) }}</td>
            <td>${{ number_format($tournament['free_credit_prize']/100, 2) }}</td>
		</tr>

		@endforeach
	</tbody>
</table>
{!! $tournaments->render() !!}
@else
<p>There are no tournaments to display</p>
@endif