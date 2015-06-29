@extends('admin.layouts.scaffold')

@section('main')

<h1>Create Bet</h1>

{!! Form::open(array('route' => 'bets.store')) }}
    <ul>
        <li>
            {!! Form::label('user_id', 'User_id:') }}
            {!! Form::input('number', 'user_id') }}
        </li>

        <li>
            {!! Form::label('bet_amount', 'Bet_amount:') }}
            {!! Form::input('number', 'bet_amount') }}
        </li>

        <li>
            {!! Form::submit('Submit', array('class' => 'btn')) }}
        </li>
    </ul>
{!! Form::close() }}

@if ($errors->any())
    <ul>
        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
    </ul>
@endif

@stop


