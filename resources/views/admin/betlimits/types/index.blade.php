@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Bet Limit Types</h2>

		@if ($betlimits->count())
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Bet Type</th>
					<th>Default Amount</th>
					<th>Notes</th>
					<th colspan='2'>Action</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($betlimits as $betlimit)
                <tr>
					<td>{{{ $betlimit->betType->name }}}</td>
					<td>${{{ number_format($betlimit->default_amount/100, 2) }}}</td>
					<td>{{{ $betlimit->notes }}}</td>
                    <td>{!! link_to_route('admin.bet-limits.edit', 'Edit', array($betlimit->id), array('class' => 'btn btn-info')) !!}</td>

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