@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Market: {{ $market->market_type_name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.markets.index', 'Back to Markets', array("q" => $search), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Market</h4>
		<div class='col-lg-6'>
        	{{ Form::model($market, array('method' => 'PATCH', 'route' => array('admin.markets.update', $market->id, "q" => $search))) }}
        	<div class="form-group">
                {{ Form::label('market_id', 'Market Id:') }}
                {{ Form::text('market_id', null, array('class' => 'form-control', 'placeholder' => $market->id, 'disabled')) }}
            </div>
        	<div class="form-group">
        		{{ Form::label('market_type_name', 'Market Type Name:') }}
        		{{ Form::text('market_type_name', null, array('class' => 'form-control', 'placeholder' => $market->market_type_name, 'disabled')) }}
        	</div>
        	<div class="form-group">
        		{{ Form::label('event_name', 'Event Name:') }}
        		{{ Form::text('event_name', null, array('class' => 'form-control', 'placeholder' => $market->event_name, 'disabled')) }}
        	</div>

        	<div class="form-group">
        		{{ Form::label('display_flag', 'Betting Open on Topbetta:') }}
        		{{ Form::select('display_flag', array(
                                                    '1' => 'Yes',
                                                    '0' => 'No'), $market->display_flag, array('class' => 'form-control selected', 'placeholder' => $market->display_flag)) }}
        	</div>
        	<div class="form-group">
                {{ Form::label('market_status', 'Market Status:') }}
                {{ Form::select('market_status', array(
                                    '' => 'No Status (Racing)',
                                    'O' => 'Open',
                                    'C' => 'Closed',
                                    'R' => 'Resulted',
                                    'D' => 'Deleted (Not shown in sports)'), array('class' => 'form-control', 'placeholder' => $market->market_status)) }}
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