@extends('layouts.master')

@section('main')

<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
			<h2 class="col-lg-4">Market Types <small>({{ number_format($marketTypes->getTotal()) }})</small></h2>
            <h2 class="col-lg-4 pull-right">
			{{ Form::open(array('method' => 'GET')) }}
			<div class="input-group custom-search-form">
				{{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search...")) }}
				<span class="input-group-btn">
					<button class="btn btn-default" type="button">
						<i class="fa fa-search"></i>
					</button>
				</span>
			</div>
			{{ Form::close() }}
                </h2>
        </div>
		@if (count($marketTypes))
        <table class="table table-striped table-bordered table-hover">
        	<thead>
        		<tr>
        			<th>ID</th>
        			<th>Market Type</th>
                    <th>Description</th>
        			<th>Created</th>
        			<th>Updated</th>
					<th>Ordering</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($marketTypes as $marketType)
        		<tr>
        			<td>{{ $marketType->id }}</td>
        			<td>{{ $marketType->name }}</td>
        			<td>{{ $marketType->description }}</td>
        			<td>{{ $marketType->created_at }}</td>
        			<td>{{ $marketType->updated_at }}</td>
					<td>{{ $marketType->ordering ? $marketType->ordering : '' }}</td>
        			<td>{{ link_to_route('admin.markettypes.edit', 'Edit', array($marketType->id, "q" => $search), array('class' => 'btn btn-info')) }}</td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
			{{ $marketTypes->links() }}

        @else
        <p>There are no market types to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop