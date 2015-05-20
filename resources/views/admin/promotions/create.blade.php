@extends('admin.layouts.master')

@section('main')

{!! Form::macro('datetime', function($name, $value) {
    return "<div class='input-group datepicker'>
                <input type='text' class='form-control' name='$name' id='$name' readonly/>
                <span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span>
                </span>
            </div>";
}); !!}

<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Promotion: Create New</h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{!! link_to_route('admin.promotions.index', 'Back to Promotions', array(), array('class' => 'btn btn-outline btn-warning')) !!}</span>
        </ul>
		<h4>Create Promotion</h4>
		{!! Form::open(array('method' => 'POST', 'route' => array('admin.promotions.store'))) !!}
		<div class='col-lg-6'>

        	<div class="form-group">
        		{!! Form::label('pro_code', 'Code:') !!}
        		{!! Form::text('pro_code', null, array('class' => 'form-control input-sm', 'placeholder' => 'Promotion Code')) !!}
        	</div>
        	<div class="form-group">
        		{!! Form::label('pro_description', 'Description:') !!}
        		{!! Form::text('pro_description', null, array('class' => 'form-control input-sm', 'placeholder' => 'Promotion Description')) !!}
        	</div>

			<div class="form-group">
				{!! Form::label('pro_value', 'Value:') !!}
				<div class="input-group">
					<span class="input-group-addon">$</span>
					{!! Form::text('pro_value', null, array('class' => 'form-control input-sm', 'placeholder' => 'Promotion Value')) !!}
				</div>
			</div>

			<div class="form-group">
				{!! Form::label('pro_use_once_flag', 'Use Once:') !!}
				{!! Form::select('pro_use_once_flag', array("No", "Yes"), 1, array('class' => 'form-control')) !!}
			</div>

			<div class="form-group">
				{!! Form::label('pro_status', 'Status:') !!}
				{!! Form::select('pro_status', array(0, 1), 1, array('class' => 'form-control')) !!}
			</div>

            <div class="form-group">
                {!! Form::label('pro_start_date', 'Start Date') !!}
                {!! Form::datetime('pro_start_date',null , array('class' => 'form-control input-sm datepicker','placeholder' => 'Start Date', 'readonly')) !!}
            </div>

			<div class="form-group">
				{!! Form::label('pro_end_date', 'End Date') !!}
				{!! Form::datetime('pro_end_date',null , array('class' => 'form-control input-sm datepicker','placeholder' => 'End Date', 'readonly')) !!}
			</div>

			<div class="form-group">
				{!! Form::submit('Save', array('class' => 'btn btn-info')) !!}
			</div>

		</div>

		<div class="col-lg-6">
			@if ($errors->any())
			<ul>
				{{ implode('', $errors->all('<li class="error">:message</li>')) }}
			</ul>
			@endif
		</div>

			{!! Form::close() !!}



	</div>
	<!-- /.col-lg-12 -->
</div>

<script type="text/javascript">
    $(".datepicker").datetimepicker({format: 'YYYY-MM-DD HH:mm'});
</script>


<!-- /.row -->
@stop