@extends('layouts.scaffold')

@section('main')

<h1>Show BetLimit</h1>

<p>{{ link_to_route('betLimits.index', 'Return to all betLimits') }}</p>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Name</th>
				<th>Amount</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>{{{ $betLimit->name }}}</td>
					<td>{{{ $betLimit->amount }}}</td>
                    <td>{{ link_to_route('betLimits.edit', 'Edit', array($betLimit->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('betLimits.destroy', $betLimit->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
        </tr>
    </tbody>
</table>

@stop