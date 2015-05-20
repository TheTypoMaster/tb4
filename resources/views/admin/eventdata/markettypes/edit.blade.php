@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Market Type: {{ $marketType->name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.markettypes.index', 'Back to Markets', array("q"=>$search), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Market Type</h4>
		<div class='col-lg-6'>
        	{{ Form::model($marketType, array('method' => 'PATCH', 'route' => array('admin.markettypes.update', $marketType->id, "q"=>$search))) }}
        	<div class="form-group">
                {{ Form::label('market_type_id', 'Market Type Id:') }}
                {{ Form::text('market_type_id', null, array('class' => 'form-control', 'placeholder' => $marketType->id, 'disabled')) }}
            </div>
			<div class="form-group">
				{{ Form::label('name', 'Name:') }}
				{{ Form::text('name', null, array('class' => 'form-control', 'placeholder' => $marketType->name)) }}
			</div>
			<div class="form-group">
				{{ Form::label('description', 'Description:') }}
				{{ Form::text('description', null, array('class' => 'form-control', 'placeholder' => $marketType->description)) }}
			</div>
			<div class="form-group">
				{{ Form::label('ordering', 'Ordering:') }}
				{{ Form::text('ordering', null, array('class' => 'form-control', 'placeholder' => $marketType->ordering)) }}
			</div>
		</div>

        <div class="col-lg-12">
        	<div class="form-group">
        		{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
        	</div>
        	{{ Form::close() }}
        </div>
        @if ($errors->any())
        <ul>
        	{{ implode('', $errors->all('<li class="error">:message</li>')) }}
        </ul>
        @endif


	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop