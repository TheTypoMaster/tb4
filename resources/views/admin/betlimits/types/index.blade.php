@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Bet Limit Types</h2>

		<p>{!! link_to_route('admin.bet-limits.create', 'Add new betLimit', null, array('class' => 'btn btn-info')) }}</p>

		@if ($betlimits->count())
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Value</th>
					<th>Default Amount</th>
					<th>Notes</th>
					<th colspan='2'>Action</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($betlimits as $betlimit)
                <tr>
                    <td>{{{ $betlimit->name }}}</td>
					<td>{{{ $betlimit->value }}}</td>
					<td>{{{ $betlimit->default_amount }}}</td>
					<td>{{{ $betlimit->notes }}}</td>
                    <td>{!! link_to_route('admin.bet-limits.edit', 'Edit', array($betlimit->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('admin.bet-limits.destroy', $betlimit->id))) }}
						{!! Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {!! Form::close() }}
                    </td>
                </tr>
				@endforeach
			</tbody>
		</table>
		@else
		There are no betLimits
		@endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop