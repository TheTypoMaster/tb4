@extends('layouts.master')

@section('main')

<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
        			<h2 class="col-lg-4">Markets <small>({{ number_format($markets->getTotal()) }})</small></h2>

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
		@if (count($markets))
        <table class="table table-striped table-bordered table-hover">
        	<thead>
        		<tr>
        			<th>ID</th>
        			<th>Market Name</th>
                    <th>Event Name</th>
        			<th>Market Status</th>
        			<th>Display</th>
        			<th>Created</th>
        			<th>Updated</th>
        			<th colspan="1">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($markets as $market)
        		<tr>
        			<td>{{ $market->id }}</td>
        			<td>{{ $market->market_type_name }}</td>
        			<td>{{ $market->event_name }}</td>
                    <td>{{ $market->market_status }}</td>
        			<td>{{ ($market->display_flag) ? 'Yes' : 'No' }}</td>
        			<td>{{ $market->created_at }}</td>
        			<td>{{ $market->updated_at }}</td>
        			<td>{{ link_to_route('admin.markets.edit', 'Edit', array($market->id), array('class' => 'btn btn-info')) }}</td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {{ $markets->appends(array('q' => $search))->links() }}
        @else
        <p>There are no markets to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop