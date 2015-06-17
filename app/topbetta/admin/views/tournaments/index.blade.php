@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<div class="row page-header">
			<h2 class="col-lg-4">Tournaments <small>{{ number_format($tournaments->getTotal()) }}</small>
                {{ link_to_route('admin.tournaments.create', 'Create', array(), array('class' => 'btn btn-info')) }}
            </h2>
            <h2 class="col-lg-4 pull-right">
                {{ Form::open(array('method' => 'GET')) }}
                <div class="input-group custom-search-form">
                    {{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search (id,name)...")) }}
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                {{ Form::close() }}
            </h2>
		</div>		
		@include('admin::tournaments.partials.tournament-list')

	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop	