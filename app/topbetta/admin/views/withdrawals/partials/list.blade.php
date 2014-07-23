@if (count($withdrawals))
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Requester Name</th>
			<th>Requester Username</th>
			<th>Requester ID</th>
			<th>Amount</th>
			<th>Withdrawal Type</th>
			<th>Requested Date</th>
			<th>Fulfilled Date</th>
			<th>Approved</th>
			<th>Notes</th>
		</tr>
	</thead>

	<tbody>
		@foreach($withdrawals as $withdrawal)
		<tr>
			<td>{{ $withdrawal->id }}</td>
			<td>{{ $withdrawal->user->name }}</td>
			<td>{{ $withdrawal->user->username }}</td>
			<td>{{ $withdrawal->user->id }}</td>
			<td>${{ number_format($withdrawal->amount / 100, 2) }}</td>
			<td>{{ ($withdrawal->withdrawal_type_id == 2) ? '(paypal) ' . $withdrawal->paypal->paypal_id : $withdrawal->type->name }}</td>
			<td>{{ $withdrawal->requested_date }}</td>
			<td>{{ $withdrawal->fulfilled }}</td>
			<td>{{ (isset($withdrawal->approved_flag)) ? ($withdrawal->approved_flag == 1) ? 'Y' : 'N' : 'Pending' }}</td>
			<td>{{{ $withdrawal->notes }}}</td>
		</tr>

		@endforeach
	</tbody>
</table>
{{ $withdrawals->links() }}
@else
<p>There are no withdrawal requests to display</p>
@endif