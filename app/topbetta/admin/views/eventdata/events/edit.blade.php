@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Event: {{ $event->name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.events.index', 'Back to Events', array(), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Event</h4>
		<div class='col-lg-6'>
        	{{ Form::model($event, array('method' => 'PATCH', 'route' => array('admin.events.update', $event->id))) }}
        	<div class="form-group">
        		{{ Form::label('name', 'Event Name:') }}
        		{{ Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) }}
        	</div>
        	<div class="form-group">
                {{ Form::label('number', 'Number:') }}
                {{ Form::text('number', null, array('class' => 'form-control', 'placeholder' => 'Number')) }}
            </div>
            <div class="form-group">
                {{ Form::label('class', 'Class:') }}
                {{ Form::text('class', null, array('class' => 'form-control', 'placeholder' => 'Class')) }}
            </div>
            <div class="form-group">
                {{ Form::label('distance', 'Distance:') }}
                {{ Form::text('distance', null, array('class' => 'form-control', 'placeholder' => 'Distance')) }}
            </div>
            <div class="form-group">
                {{ Form::label('paid_flag', 'Paid:') }}
                {{ Form::text('paid_flag', null, array('class' => 'form-control', 'placeholder' => 'Paid')) }}
            </div>
            <div class="form-group">
                {{ Form::label('start_date', 'Start Date:') }}
                {{ Form::text('start_date', null, array('class' => 'form-control', 'placeholder' => 'Start Date')) }}
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