@if (count($competitions))
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
			<td>{{ TopBetta\TournamentTicket::countTournamentEntrants($tournament->id) }}</td>
			<td>{{ ($tournament->status_flag) ? 'Active' : 'Inactive' }}</td>
		</tr>

		@endforeach
	</tbody>
</table>
{{ $tournaments->links() }}
@else
<p>There are no tournaments to display</p>
@endif