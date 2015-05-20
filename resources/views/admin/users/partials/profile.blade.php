<div class='col-lg-6'>
	{!! Form::model($user, array('method' => 'PATCH', 'route' => array('admin.users.update', $user->id))) !!}
	<div class="form-group">
		{!! Form::label('name', 'Name:') !!}
		{!! Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) !!}
	</div>				
	<div class="form-group">
		{!! Form::label('username', 'Username:') !!}
		{!! Form::text('username', null, array('class' => 'form-control', 'placeholder' => 'Username')) !!}
	</div>				
	<div class="form-group">
		{!! Form::label('email', 'Email:') !!}
		{!! Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email')) !!}
	</div>											
</div>

<div class='col-lg-6'>
	<div class="form-group">
		{!! Form::label('first-name', 'First Name:') !!}
		{!! Form::text('first-name', $user->topbettaUser->first_name, array('class' => 'form-control', 'placeholder' => 'First Name')) !!}
	</div>				
	<div class="form-group">
		{!! Form::label('last-name', 'Last Name:') !!}
		{!! Form::text('last-name', $user->topbettaUser->last_name, array('class' => 'form-control', 'placeholder' => 'Last Name')) !!}
	</div>				
	<div class="form-group">
		{!! Form::label('mobile', 'Mobile:') !!}
		{!! Form::text('mobile', $user->topbettaUser->msisdn, array('class' => 'form-control', 'placeholder' => 'Mobile')) !!}
	</div>				
</div>	
<div class="col-lg-12">
	<div class="form-group">
		{!! Form::submit('Update', array('class' => 'btn btn-info')) !!}		
	</div>						
	{!! Form::close() !!}
</div>
@if ($errors->any())
<ul>
	{{ implode('', $errors->all('<li class="error">:message</li>')) }}
</ul>
@endif