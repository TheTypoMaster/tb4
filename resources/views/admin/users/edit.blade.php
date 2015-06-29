@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		@include('admin.users.partials.header')
		<h4>Edit Profile</h4>
		@include('admin.users.partials.profile')
		<div class="tab-content">
			<div class="tab-pane fade" id="tournaments-tab">
				<h4>Tournaments</h4>
			</div>
			<div class="tab-pane fade" id="account-transactions-tab">
				<h4>Account Transactions</h4>
				<div class="col-lg-12">
					<div class="well well-sm">
						<span><b>Account Balance:</b> ${{ number_format($user->accountTransactions()->sum('amount') / 100, 2) }}</span>
					</div>	
				</div>				
			</div>
			<div class="tab-pane fade" id="free-credit-transactions-tab">
				<h4>Free Credit/Tournament Transactions</h4>
				<div class="col-lg-12">
					<div class="well well-sm">
						<span><b>Free Credit Balance:</b> ${{ number_format($user->freeCreditTransactions()->sum('amount') / 100, 2) }}</span>
					</div>	
				</div>				
			</div>
		</div>		

	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop