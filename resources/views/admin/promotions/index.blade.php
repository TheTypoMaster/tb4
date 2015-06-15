@extends('admin.layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
        			<h2 class="col-lg-4">Promotions <small>({{ number_format($promotions->total()) }})</small>
        			    {!! link_to_route('admin.promotions.create', 'New', null, array('class' => 'btn btn-info')) !!}
        			</h2>

            <h2 class="col-lg-4 pull-right">
        			{!! Form::open(array('method' => 'GET')) !!}
        			<div class="input-group custom-search-form col-lg-4 pull-right">
        				{!! Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search...")) !!}
        				<span class="input-group-btn">
        					<button class="btn btn-default" type="button">
        						<i class="fa fa-search"></i>
        					</button>
        				</span>
        			</div>
        			{!! Form::close() !!}
                </h2>
        		</div>
		@if (count($promotions))
        <table class="table table-striped table-bordered table-hover">
        	<thead>
        		<tr>
        			<th>ID</th>
        			<th>Promotion Code</th>
        			<th>Value</th>
        			<th>Description</th>
        			<th>Use Once</th>
        			<th>Start Date</th>
        			<th>End Date</th>
        			<th>Entered By</th>
        			<th>Entered On</th>
        			<th>Status</th>
        			<th colspan="2">Action</th>
        		</tr>
        	</thead>

        	<tbody>
        		@foreach($promotions as $promotion)
        		<tr>
        			<td>{{ $promotion->pro_id }}</td>
        			<td>{{ $promotion->pro_code }}</td>
        			<td>${{ number_format($promotion->pro_value, 2) }}</td>
        			<td>{{ $promotion->Pro_description }}</td>
        			<td>{{ $promotion->pro_use_once_flag ? 'Yes' : 'No' }}</td>
        			<td>{{ $promotion->pro_start_date }}</td>
        			<td>{{ $promotion->pro_end_date }}</td>
        			<td>{{ $promotion->user->name }}</td>
        			<td>{{ $promotion->pro_entered_date}}</td>
        			<td>{{ $promotion->pro_status }}</td>
        			<td>{!! link_to_route('admin.promotions.edit', 'Edit', array($promotion->pro_id, "q" => $search), array('class' => 'btn btn-info')) !!}</td>
					<td>
						{!! Form::open(array("method" => "DELETE", "route" => array("admin.promotions.destroy", $promotion->pro_id, "q"=>$search), "class"=>"delete-promotion")) !!}
						<button class="btn btn-danger"type="submit">Delete</button>
						{!! Form::close() !!}
					</td>

        		</tr>
        		@endforeach
        	</tbody>
        </table>
        {!! $promotions->appends(array('q' => $search))->render() !!}
        @else
        <p>There are no promotions to display</p>
        @endif
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script type="text/javascript">
	$(".delete-promotion").submit(function(e){
		return confirm("Are you sure you want to delete this promotion") ? true : false;
	});
</script>
@stop