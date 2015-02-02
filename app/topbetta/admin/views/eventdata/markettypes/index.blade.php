@extends('layouts.master')

@section('main')

<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
        			<h2 class="col-lg-4">Market Types <small>({{ number_format($marketTypes->getTotal()) }})</small></h2>

        			
		@if (count($marketTypeTypess))
        <table class="table table-striped table-bordered table-hover">
        	<thead>
        		<tr>
        			<th>ID</th>
        			<th>Market Type</th>
                    <th>Description</th>
        			<th>Created</th>
        			<th>Updated</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($marketTypeTypes as $marketTypeType)
        		<tr>
        			<td>{{ $marketType->id }}</td>
        			<td>{{ $marketType->name }}</td>
        			<td>{{ $marketType->description }}</td>
        			<td>{{ $marketType->created_at }}</td>
        			<td>{{ $marketType->updated_at }}</td>
        			<td>{{ link_to_route('admin.markets.edit', 'Edit', array($marketType->id), array('class' => 'btn btn-info')) }}</td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {{ $marketTypes->appends(array('q' => $search))->links() }}
        @else
        <p>There are no markets to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop