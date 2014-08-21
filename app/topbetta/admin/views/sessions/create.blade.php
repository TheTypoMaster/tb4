@extends('layouts.guest')

@section('main')
<div class="col-md-4 col-md-offset-4">
    <h1>TopBetta Admin <small>v0.1</small></h1>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Please sign in</h3>
        </div>
        <div class="panel-body">
                {{ Form::open(array('route' => 'admin.session.store')) }}
                <fieldset>
                    <div class="form-group">
                        {{ Form::text('username', null, array('class' => 'form-control', 'placeholder' => 'Username')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) }}
                    </div>
                    {{-- <div class="checkbox">
                        <label>
                            <input name="remember" type="checkbox" value="Remember Me"> Remember Me
                        </label>
                    </div> --}}
                    {{ Form::submit('Login', array('class' => 'btn btn-lg btn-success btn-block')) }}
                </fieldset>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
