@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Competition: {{ $competition->name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.competitions.index', 'Back to Competitions', array(), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Competition</h4>
		<div class='col-lg-6'>
        	{{ Form::model($competition, array('method' => 'PATCH', 'route' => array('admin.competitions.update', $competition->id))) }}
        	<div class="form-group">
        		{{ Form::label('name', 'Competition Name:') }}
        		{{ Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) }}
        	</div>
        	<div class="form-group">
                {{ Form::label('state', 'State:') }}
                {{ Form::text('state', null, array('class' => 'form-control', 'placeholder' => 'State')) }}
            </div>
            <div class="form-group">
                {{ Form::label('track', 'Track:') }}
                {{ Form::text('track', null, array('class' => 'form-control', 'placeholder' => 'Track')) }}
            </div>
            <div class="form-group">
                {{ Form::label('weather', 'Weather:') }}
                {{ Form::text('weather', null, array('class' => 'form-control', 'placeholder' => 'Weather')) }}
            </div>
            <div class="form-group">
                {{ Form::label('type_code', 'Type:') }}
                {{ Form::text('type_code', null, array('class' => 'form-control', 'placeholder' => 'Type')) }}
            </div>
            <div class="form-group">
                {{ Form::label('meeting_grade', 'Grade:') }}
                {{ Form::text('meeting_grade', null, array('class' => 'form-control', 'placeholder' => 'Grade')) }}
            </div>
            <div class="form-group">
                {{ Form::label('country', 'Country:') }}
                {{ Form::text('country', null, array('class' => 'form-control', 'placeholder' => 'Country')) }}
            </div>
            <div class="form-group">
                {{ Form::label('start_date', 'Start Date/Time:') }}
                {{ Form::text('start_date', null, array('class' => 'form-control', 'placeholder' => 'Start')) }}
            </div>
            <div class="form-group">
                {{ Form::label('close_time', 'Close Date/Time:') }}
                {{ Form::text('close_time', null, array('class' => 'form-control', 'placeholder' => 'Close')) }}
            </div>
            <div class="form-group">
                {{ Form::label('display_flag', 'Display on Topbetta:') }}
                {{ Form::text('display_flag', null, array('class' => 'form-control', 'placeholder' => 'Display')) }}
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