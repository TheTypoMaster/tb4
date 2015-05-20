@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
        			<h2 class="col-lg-4">Sports <small>({{ number_format($sports->getTotal()) }})</small></h2>

        			{{ Form::open(array('method' => 'GET')) }}
        			<div class="input-group custom-search-form col-lg-4 pull-right">
        				{{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search...")) }}
        				<span class="input-group-btn">
        					<button class="btn btn-default" type="button">
        						<i class="fa fa-search"></i>
        					</button>
        				</span>
        			</div>
        			{{ Form::close() }}
        		</div>
		@if (count($sports))
        <table class="table table-striped table-bordered table-hover">
        	<thead>
        		<tr>
        			<th>ID</th>
        			<th>Sport Name</th>
        			<th>Description</th>
        			<th>Status</th>
        			<th>Is Racing</th>
        			<th>Created</th>
        			<th>Updated</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($sports as $sport)
        		<tr>
        			<td>{{ $sport->id }}</td>
        			<td>{{ $sport->name }}</td>
        			<td>{{ $sport->description }}</td>
        			<td>{{ ($sport->status_flag) ? 'Yes' : 'No' }}</td>
        			<td>{{ ($sport->racing_flag) ? 'Yes' : 'No' }}</td>
        			<td>{{ $sport->created_at }}</td>
        			<td>{{ $sport->updated_at }}</td>
        			<td>{{ link_to_route('admin.sports.edit', 'Edit', array($sport->id, "q" => $search), array('class' => 'btn btn-info')) }}</td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {{ $sports->appends(array('q' => $search))->links() }}
        @else
        <p>There are no sports to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop