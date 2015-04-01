@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Selection: {{ $selection->name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.selections.index', 'Back to Selections', array('q'=>$search), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Selection</h4>
		<div class='col-lg-6'>
        	{{ Form::model($selection, array('method' => 'PATCH', 'route' => array('admin.selections.update', $selection->id, "q" => $search))) }}
        	<div class="form-group">
                {{ Form::label('id', 'Selection Id:') }}
                {{ Form::text('id', null, array('class' => 'form-control', 'placeholder' => $selection->id, 'disabled')) }}
            </div>
        	<div class="form-group">
        		{{ Form::label('name', 'Selection Name:') }}
        		{{ Form::text('name', null, array('class' => 'form-control', 'placeholder' => $selection->name)) }}
        	</div>
        	<div class="form-group">
        		{{ Form::label('event_name', 'Event Name:') }}
        		{{ Form::text('event_name', null, array('class' => 'form-control', 'placeholder' => $selection->event_name, 'disabled')) }}
        	</div>
            <div class="form-group">
                {{ Form::label('competition_name', 'Competition Name:') }}
                {{ Form::text('competition_name', null, array('class' => 'form-control', 'placeholder' => $selection->competition_name, 'disabled')) }}
            </div>
            <div class="form-group">
                {{ Form::label('team.first.id', 'Team:') }}
                {{ Form::select('team.first.id', $teams, null, array('class' => 'form-control select2')) }}
            </div>
        	<div class="form-group">
        		{{ Form::label('selection_status_id', 'Selection Status:') }}
        		{{ Form::select('selection_status_id', array(
                    '1' => 'Not Scratched',
                    '2' => 'Scratched',
                    '3' => 'Late Scratching',
                    '4' => 'Suspended'), null, array('class' => 'form-control', 'placeholder' => $selection->selection_status_id)) }}
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