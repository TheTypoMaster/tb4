@extends('layouts.default')
@section('body')
{{ Form::open(array('url' => 'login', 'method' => 'post')) }}
{{Form::label('username','Username')}}
{{Form::text('username', null,array('class' => 'form-control'))}}
{{Form::label('password','Password')}}
{{Form::password('password',array('class' => 'form-control'))}}
{{Form::submit('Login', array('class' => 'btn btn-primary'))}}
{{ Form::close() }}


@stop