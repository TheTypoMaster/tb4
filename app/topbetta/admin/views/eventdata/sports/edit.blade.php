@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Sport: {{ $sport->name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.sports.index', 'Back to Sports', array(), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Sport</h4>
		<div class='col-lg-6'>
        	{{ Form::model($sport, array('method' => 'PATCH', 'route' => array('admin.sports.update', $sport->id))) }}
        	<div class="form-group">
        		{{ Form::label('name', 'Sport Name:') }}
        		{{ Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) }}
        	</div>
        	<div class="form-group">
        		{{ Form::label('description', 'Sport Description:') }}
        		{{ Form::text('description', null, array('class' => 'form-control', 'placeholder' => 'Description')) }}
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