@if ($betLimits->count())
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Type</th>
			<th>Amount</th>
			<th>Notes</th>
			<th colspan="2">Action</th>
		</tr>
	</thead>

	<tbody>
		@foreach ($betLimits as $betLimit)
		<tr>
			<td>{{ $betLimitTypes[$betLimit->bet_limit_type_id] }}</td>
			<td>{{ ($betLimit->limitType->name != 'bet_flexi') ? '$' . number_format($betLimit->amount, 2) : $betLimit->amount . '%' }}</td>
			<td>{{ $betLimit->notes }}</td>
			<td>{{ link_to_route('admin.users.bet-limits.edit', 'Edit', array($user->id, $betLimit->id), array('class' => 'btn btn-info')) }}</td>
			<td>
				{{ Form::open(array('method' => 'DELETE', 'route' => array('admin.users.bet-limits.destroy', $user->id, $betLimit->id))) }}
				{{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
				{{ Form::close() }}
			</td>			
		</tr>
		@endforeach
	</tbody>
</table>
{{ $betLimits->links() }}
@else
<div class="row col-lg-12">
	<p>There are no bet limits to display</p>
</div>
@endif