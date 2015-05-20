@extends('layouts.scaffold')

@section('main')

<h1>Edit Bet</h1>
{{ Form::model($bet, array('method' => 'PATCH', 'route' => array('bets.update', $bet->id))) }}
    <ul>
        <li>
            {{ Form::label('user_id', 'User_id:') }}
            {{ Form::input('number', 'user_id') }}
        </li>

        <li>
            {{ Form::label('bet_amount', 'Bet_amount:') }}
            {{ Form::input('number', 'bet_amount') }}
        </li>

        <li>
            {{ Form::submit('Update', array('class' => 'btn btn-info')) }}
            {{ link_to_route('bets.show', 'Cancel', $bet->id, array('class' => 'btn')) }}
        </li>
    </ul>
{{ Form::close() }}

@if ($errors->any())
    <ul>
        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
    </ul>
@endif

@stop