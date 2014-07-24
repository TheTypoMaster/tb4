@extends('layouts.scaffold')

@section('main')

<h1>Show Bet</h1>

<p>{{ link_to_route('bets.index', 'Return to all bets') }}</p>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>User_id</th>
				<th>Bet_amount</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>{{{ $bet->user_id }}}</td>
					<td>{{{ $bet->bet_amount }}}</td>
                    <td>{{ link_to_route('bets.edit', 'Edit', array($bet->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('bets.destroy', $bet->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
        </tr>
    </tbody>
</table>

@stop