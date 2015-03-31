<span class="pull-right">{{ link_to_route('admin.users.account-transactions.create', 'Create', array($user->id), array("class" => "btn btn-outline btn-success")) }}</span>
@if ($transactions->count())
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Recipient Name</th>
			<th>Recipient Username</th>
			<th>Recipient ID</th>
			<th>Giver Name</th>
			<th>Giver Username</th>
			<th>Giver ID</th>
			<th>Transaction Type</th>
			<th>Transaction Date</th>
			<th>Amount</th>
			<th>Notes</th>
		</tr>
	</thead>

	<tbody>
		@foreach($transactions as $transaction)
		{{-- var_dump($transaction) --}}
		<tr>
			<td>{{ $transaction->id }}</td>
			<td>{{ $transaction->recipient->name }}</td>
			<td>{{ $transaction->recipient->username }}</td>
			<td>{{ $transaction->recipient_id }}</td>
			<td>{{ (isset($transaction->giver->name)) ? $transaction->giver->name : '-' }}</td>
			<td>{{ (isset($transaction->giver->username)) ? $transaction->giver->username : '-' }}</td>
			<td>{{ $transaction->giver_id }}</td>
			<td>{{ $transaction->transactionType->name }}</td>
			<td>{{ $transaction->created_date }}</td>
			<td>${{ number_format($transaction->amount / 100, 2) }}</td>
			<td>{{{ $transaction->notes }}}</td>
		</tr>

		@endforeach
	</tbody>
</table>
{{ $transactions->links() }}
@else
<p>There are no transactions to display</p>
@endif