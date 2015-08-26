@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		@include('admin.users.partials.header')

        <div class="alert alert-danger col-lg-12" style="position:relative">
            <p><span class="glyphicon glyphicon-warning-sign"></span> The <b>LOWEST LIMIT</b> amount that matches will take affect</p>
        </div>

		<div class="col-lg-1">
			{!! link_to_route('admin.users.bet-limits.create', 'Add', array($user->id), array('class' => 'btn btn-success')) !!}
		</div>		

		@include('admin.betlimits.user.partials.bet-limits')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop