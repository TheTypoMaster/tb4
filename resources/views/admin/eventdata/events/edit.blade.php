@extends('layouts.master')

@section('main')

    {{ Form::macro('start_datetime', function($value) use ($event) {
        return "<div class='input-group datepicker'>
                    <input type='text' class='form-control' name='start_date' id='start_date' readonly value='$event->start_date'/>
                    <span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span>
                    </span>
                </div>";
                }); }}

<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Event: {{ $event->name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.events.index', 'Back to Events', array('q'=>$search), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Event</h4>
		<div class='col-lg-6'>
        	{{ Form::model($event, array('method' => 'PATCH', 'route' => array('admin.events.update', $event->id, 'q' => $search))) }}
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
                {{ Form::select('paid_flag', array(
                                '1' => 'Yes',
                                '0' => 'No'), $event->paid,
                                array('class' => 'form-control selected', 'placeholder' => $event->paid)) }}
            </div>

            <div class="form-group">
                {{ Form::label('start_date', 'Start Date') }}
                {{ Form::start_datetime('start_date', null, array('class' => 'form-control input-sm datepicker','placeholder' => $event->start_date, 'readonly'))}}
            </div>

            <div class="form-group">
                {{ Form::label('event_status_id', 'Event Status') }}
                {{ Form::select('event_status_id', $event_status, null, array('class' => 'form-control'))}}
            </div>

            <div class="form-group">
                {{ Form::label('display_flag', 'Display on Topbetta:') }}
                {{ Form::select('display_flag', array(
                                '1' => 'Yes',
                                '0' => 'No'), $event->display_flag,
                                array('class' => 'form-control selected', 'placeholder' => $event->display_flag)) }}
            </div>

            @foreach(array(0,1) as $i)
                <div class="form-group teams-template form-inline">
                    {{ Form::label('teams', 'Team: ') }}
                    {{ Form::select("teams[]", array(0 => null) + $teams->lists('name', 'id'), array_get($event->teams->toArray(), $i . '.id', null), array("class" => "form-control select2")) }}
                    {{ Form::select("team_position[]", array(0 => null, 'home' => 'home', 'away' => 'away'), array_get($event->teams->toArray(), $i . '.pivot.team_position', null), array('class' => 'form-control')) }}
                </div>
            @endforeach

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

<script type="text/javascript">
    $(".datepicker").datetimepicker({format: 'YYYY-MM-DD HH:mm'});
</script>

<!-- /.row -->
@stop