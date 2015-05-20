@extends('admin.layouts.master')

@section('main')

{!! Form::macro('datetime', function($value) {
    return "<div class='input-group datepicker'>
                <input type='text' class='form-control' name='start_date' id='start_date' readonly/>
                <span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span>
                </span>
            </div>";
}); !!}

<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Competition: Create New</h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{!! link_to_route('admin.competitions.index', 'Back to Competitions', array(), array('class' => 'btn btn-outline btn-warning')) !!}</span>
        </ul>
		<h4>Create Competition</h4>
		<div class='col-lg-6'>
        	{!! Form::open(array('method' => 'POST', 'route' => array('admin.competitions.store'))) !!}
        	<div class="form-group">
        		{!! Form::label('sport_id', 'Sport:') !!}
        		{!! Form::select('sport_id', $sports) !!}
        	</div>
        	<div class="form-group">
        		{!! Form::label('competition_name', 'Competition/Meeting Name') !!}
        		{!! Form::text('competition_name', null, array('class' => 'form-control input-sm', 'placeholder' => 'The exact name that the data feed will use')) !!}
        	</div>

            <div class="form-group">
                {!! Form::label('start_date', 'Start Date') !!}
                {!! Form::datetime('start_date',null , array('class' => 'form-control input-sm datepicker','placeholder' => 'Start Date', 'readonly')) !!}

            </div>

      <div class="col-lg-12">
        	<div class="form-group">
        		{!! Form::submit('Save', array('class' => 'btn btn-info')) !!}
        	</div>
        	{!! Form::close() !!}
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