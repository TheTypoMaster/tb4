@if (count($withdrawals))
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Requester Name</th>
			<th>Requester Username</th>
			<th>Requester ID</th>
			<th>Request Amount</th>
            <th>Account Balance</th>
            <th>Available to Withdraw</th>
			<th>Withdrawal Type</th>
			<th>Requested Date</th>
			<th>Fulfilled Date</th>
			<th>Approved</th>
			<th>Notes</th>
            <th>Action</th>
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
            <td>${{ (isset($withdrawal->processed_amount)) ? number_format($withdrawal->processed_amount/100, 2) : number_format($withdrawal->user->accountBalance()/100, 2) }}</td>
            <td>${{ number_format($withdrawal->user->topbettauser ? ($withdrawal->user->accountBalance() - $withdrawal->user->topbettauser->balance_to_turnover)/100 : 0, 2) }}</td>
			<td>{{ ($withdrawal->withdrawal_type_id == 2) ? '(paypal) ' . $withdrawal->paypal->paypal_id : $withdrawal->type->name }}</td>
			<td>{{ $withdrawal->requested_date }}</td>
			<td>{{ $withdrawal->fulfilled_date }}</td>
			<td>{{ (isset($withdrawal->approved_flag)) ? ($withdrawal->approved_flag == 1) ? 'Yes' : 'No' : 'Pending' }}</td>
			<td>{{{ $withdrawal->notes }}}</td>
            <td>
                @if(is_null($withdrawal->approved_flag))
                    {!! link_to_route("admin.withdrawals.edit", "Process", array($withdrawal->id), array("class" => "btn btn-primary")) !!}
                @endif
            </td>
		</tr>

		@endforeach
	</tbody>
</table>
{!! $withdrawals->appends(array("pending" => isset($pending) ? $pending : ''))->render() !!}
@else
<p>There are no withdrawal requests to display</p>
@endif