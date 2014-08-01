@if ($bets->count())
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Username</th>
			<th>Bet Date</th>
			<th>Selections</th>
			<th>Bet Type</th>
			<th>Bet Amount</th>
			<th>Dividend</th>
			<th>Paid</th>
			<th>Result</th>
		</tr>
	</thead>

	<tbody>
		@foreach ($bets as $bet)
		<tr>
			<td>{{{ $bet->id }}}</td>
			<td>{{ $bet->user->username }}</td>
			<td>{{{ $bet->created_date }}}</td>
			<td>{{ ($bet->bet_type_id < 4) ? (isset($bet->selections[0]->selection->name)) ? $bet->selections[0]->selection->name : 'n/a' : $bet->selection_string }}</td>
			<td>{{ $bet->betType->name }}</td>
			<td>${{ number_format($bet->bet_amount / 100, 2) }}</td>
			<td>-</td>
			<td>${{ ($bet->payout) ? number_format($bet->payout->amount / 100, 2) : 0}}</td>
			<td>{{ $bet->status->name }}</td>
		</tr>

		@endforeach
	</tbody>
</table>
{{ $bets->links() }}
@else
<p>There are no bets to display</p>
@endif