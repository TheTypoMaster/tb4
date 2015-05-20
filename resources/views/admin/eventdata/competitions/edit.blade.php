@extends('layouts.master')

@section('main')

    {{ Form::macro('start_datetime', function($value) use ($competition) {
        return "<div class='input-group datepicker'>
                    <input type='text' class='form-control' name='start_date' id='start_date' readonly value='$competition->start_date'/>
                    <span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span>
                    </span>
                </div>";
                }); }}

    {{ Form::macro('close_datetime', function($value) use ($competition){
    return "<div class='input-group datepicker'>
                <input type='text' class='form-control' name='close_time' id='close_time' readonly value='$competition->close_time'/>
                <span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span>
                </span>
            </div>";
            }); }}


<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Competition: {{ $competition->name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.competitions.index', 'Back to Competitions', array(), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Competition</h4>
		<div class='col-lg-6'>
        	{{ Form::model($competition, array('method' => 'PATCH', 'route' => array('admin.competitions.update', $competition->id, "q" => $search))) }}
        	<div class="form-group">
        		{{ Form::label('name', 'Competition Name:') }}
        		{{ Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) }}
        	</div>
        	{{--<div class="form-group">--}}
                {{--{{ Form::label('state', 'State:') }}--}}
                {{--{{ Form::text('state', null, array('class' => 'form-control', 'placeholder' => 'State')) }}--}}
            {{--</div>--}}
            <div class="form-group">
                {{ Form::label('state', 'State:') }}
                {{ Form::select('state', array(
                                '' => 'N/A',
                                'ACT' => 'ACT',
                                'NSW' => 'NSW',
                                'NT' => 'Northen Territory',
                                'QLD' => 'Queensland',
                                'SA' => 'South Australia',
                                'TAS' => 'Tasmaina',
                                'VIC' => 'Victoria',
                                'WA' => 'Western Australia'
                                ),
                                $competition->state,
                                array('class' => 'form-control selected', 'placeholder' => $competition->state)) }}
            </div>
            <div class="form-group">
                {{ Form::label('track', 'Track:') }}
                {{ Form::text('track', null, array('class' => 'form-control', 'placeholder' => 'Track')) }}
            </div>
            <div class="form-group">
                {{ Form::label('weather', 'Weather:') }}
                {{ Form::text('weather', null, array('class' => 'form-control', 'placeholder' => 'Weather')) }}
            </div>
            {{--<div class="form-group">--}}
                {{--{{ Form::label('type_code', 'Type:') }}--}}
                {{--{{ Form::text('type_code', null, array('class' => 'form-control', 'placeholder' => 'Type')) }}--}}
            {{--</div>--}}
            <div class="form-group">
                {{ Form::label('type_code', 'Type:') }}
                {{ Form::select('type_code', array(
                                '' => 'N/A',
                                'R' => 'Gallops',
                                'H' => 'Harness',
                                'G' => 'Greyhounds'),
                                $competition->type_code,
                                array('class' => 'form-control selected', 'placeholder' => $competition->type_code)) }}
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
                {{ Form::label('start_date', 'Start Date') }}
                {{ Form::start_datetime('start_date', null, array('class' => 'form-control input-sm datepicker','placeholder' => $competition->start_date, 'readonly'))}}
            </div>

            <div class="form-group">
                {{ Form::label('close_time', 'Close Date') }}
                {{ Form::close_datetime('close_time', null , array('class' => 'form-control input-sm datepicker','placeholder' => $competition->close_time, 'readonly'), $competition->close_time)}}
            </div>

            <div class="form-group">
                {{ Form::label('display_flag', 'Display on Topbetta:') }}
                {{ Form::select('display_flag', array(
                                '1' => 'Yes',
                                '0' => 'No'),
                                $competition->display_flag,
                                array('class' => 'form-control selected', 'placeholder' => $competition->display_flag)) }}
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

<script type="text/javascript">
    $(".datepicker").datetimepicker({format: 'YYYY-MM-DD HH:mm'});
</script>

<!-- /.row -->
@stop