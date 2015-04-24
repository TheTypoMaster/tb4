@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
			<h2 class="col-lg-4">Tournaments <small>{{ number_format($tournaments->getTotal()) }}</small>
                <a href="#" class="btn btn-info">Create</a>
            </h2>

			{{ Form::open(array('method' => 'GET')) }}
			<div class="input-group custom-search-form col-lg-4 pull-right">
				{{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search (id,name)...")) }}
				<span class="input-group-btn">
					<button class="btn btn-default" type="button">
						<i class="fa fa-search"></i>
					</button>
				</span>
			</div>		
			{{ Form::close() }}
		</div>		
		@include('admin::tournaments.partials.tournament-list')
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop	