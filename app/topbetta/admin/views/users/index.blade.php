@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
			<h2 class="col-lg-4">TopBetta Users <small>{{ number_format($users->getTotal()) }}</small></h2>

			{{ Form::open(array('method' => 'GET')) }}
			<div class="input-group custom-search-form col-lg-4 pull-right">
				{{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search...")) }}
				<span class="input-group-btn">
					<button class="btn btn-default" type="button">
						<i class="fa fa-search"></i>
					</button>
				</span>
			</div>		
			{{ Form::close() }}
		</div>

		{{-- <p>{{ link_to_route('admin.users.create', 'Add new user') }}</p> --}}

		@if ($users->count())
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>Userid</th>
					<th>Username</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email</th>
					<th>Register Date</th>
					<th>DOB</th>
					<th>Mobile</th>
					<th>Home Phone</th>
					<th>Basic Account</th>
					<th>Status</th>
					<th colspan="1">Action</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($users as $user)     
				{{-- Few users don't have a topbettauser record in DB --}}
				@if(isset($user->topbettaUser))
				<tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->topbettaUser->first_name }}</td>
                    <td>{{ $user->topbettaUser->last_name }}</td>
					<td>{{ $user->email }}</td>
					<td>{{ $user->registerDate }}</td>
					<td>{{ $user->topbettaUser->dob_day }}/{{ $user->topbettaUser->dob_month }}/{{ $user->topbettaUser->dob_year }}</td>
					<td>{{ $user->topbettaUser->msisdn }}</td>
					<td>{{ $user->topbettaUser->phone_number }}</td>
					<td>{{ (!$user->isTopBetta) ? 'Yes' : 'No' }}</td>
					<td>{{ ($user->block) ? 'Blocked' : 'Active' }}</td>
                    <td>{{ link_to_route('admin.users.edit', 'Edit', array($user->id), array('class' => 'btn btn-info')) }}</td>
                    {{--<td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('admin.users.destroy', $user->id))) }}
					{{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
					{{ Form::close() }}
                    </td>--}}
                </tr>
				@else
				<tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>-</td>
                    <td>-</td>
					<td>{{ $user->email }}</td>
					<td>{{ $user->registerDate }}</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>{{ (!$user->isTopBetta) ? 'Yes' : 'No' }}</td>
					<td>{{ ($user->block) ? 'Blocked' : 'Active' }}</td>
                    <td>n/a</td>
                    {{--<td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('admin.users.destroy', $user->id))) }}
					{{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
					{{ Form::close() }}
                    </td>--}}
                </tr>				
				@endif
				@endforeach
			</tbody>
		</table>
		{{ $users->appends(array('q' => $search))->links() }}
		@else
		There are no users
		@endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop