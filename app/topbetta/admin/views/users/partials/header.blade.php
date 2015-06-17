<h2 class="page-header">User: {{ $user->topbettaUser->first_name }} {{ $user->topbettaUser->last_name }} ({{ $user->username }}) 
	<small>Acc: ${{ number_format($user->accountTransactions()->sum('amount') / 100, 2) }}
		FC: ${{ number_format($user->freeCreditTransactions()->sum('amount') / 100, 2) }}
        Withdraw: ${{ max(number_format(($user->accountTransactions()->sum('amount') - object_get($user->topbettauser, 'balance_to_turnover', 0) - object_get($user->topbettauser, 'free_credit_wins_to_turnover', 0))/100, 2), 0) }}
	</small>
    <span class='pull-right'>{{ link_to_route('admin.users.index', 'Back to Users', array(), array('class' => 'btn btn-outline btn-warning')) }}</span>
</h2>

<ul class="nav nav-tabs" >
	<li class="{{($active != 'profile') ?: 'active' }}">{{ link_to_route('admin.users.edit', 'Profile', array($user->id), array()) }}
	</li>
	<li class="{{($active != 'bet-limits') ?: 'active' }}">{{ link_to_route('admin.users.bet-limits.index', 'Bet Limits', array($user->id), array()) }}
	</li>
	<li class="{{($active != 'bets') ?: 'active' }}">{{ link_to_route('admin.users.bets.index', 'Bets', array($user->id), array()) }}
	</li>
	<li class="{{($active != 'tournaments') ?: 'active' }}">{{ link_to_route('admin.users.tournaments.index', 'Tournaments', array($user->id), array()) }}
	</li>
	<li class="{{($active != 'account-transactions') ?: 'active' }}">{{ link_to_route('admin.users.account-transactions.index', 'Account Transactions', array($user->id), array()) }}
	</li>
	<li class="{{($active != 'free-credit-transactions') ?: 'active' }}">{{ link_to_route('admin.users.free-credit-transactions.index', 'Free Credit Transactions', array($user->id), array()) }}
	</li>
	<li class="{{($active != 'withdrawals') ?: 'active' }}">{{ link_to_route('admin.users.withdrawals.index', 'Withdrawal Requests', array($user->id), array()) }}
	</li>
    <li class="{{($active != 'deposit-limit') ?: 'active' }}">{{ link_to_route('admin.users.deposit-limit.index', 'Deposit Limit', array($user->id), array()) }}
    </li>
    <li class="{{($active != 'user-permissions') ?: 'active' }}">{{ link_to_route('admin.user-permissions.edit', 'Permissions', array($user->id), array()) }}
    </li>
</ul>