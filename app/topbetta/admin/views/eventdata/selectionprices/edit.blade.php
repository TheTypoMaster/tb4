@extends('layouts.master')

@section('main')
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">Selection Prices: {{ $selectionprice->selection_name }}

        </h2>
        <ul class="nav nav-tabs">
            <span class='pull-right'>{{ link_to_route('admin.selections.index', 'Back to Selection Prices', array("q" => $search, 'market' => $market, 'event' => $event), array('class' => 'btn btn-outline btn-warning')) }}</span>
        </ul>
		<h4>Edit Price</h4>
		<div class='col-lg-6'>
        	{{ Form::model($selectionprice, array('method' => 'PATCH', 'route' => array('admin.selectionprices.update', $selectionprice->id, "q" => $search, "market" => $market))) }}
        	<div class="form-group">
                {{ Form::label('id', 'Price Id:') }}
                {{ Form::text('id', null, array('class' => 'form-control', 'placeholder' => $selectionprice->id, 'disabled')) }}
            </div>
        	<div class="form-group">
        		{{ Form::label('selection_name', 'Selection Name:') }}
        		{{ Form::text('selection_name', null, array('class' => 'form-control', 'placeholder' => $selectionprice->selection_name, 'disabled')) }}
        	</div>
        	<div class="form-group">
        		{{ Form::label('event_name', 'Event Name:') }}
        		{{ Form::text('event_name', null, array('class' => 'form-control', 'placeholder' => $selectionprice->event_name, 'disabled')) }}
        	</div>
            <div class="form-group">
                {{ Form::label('competition_name', 'Competition Name:') }}
                {{ Form::text('competition_name', null, array('class' => 'form-control', 'placeholder' => $selectionprice->competition_name, 'disabled')) }}
            </div>

            <div class="form-group">
                 {{ Form::label('win_odds', 'Win Odds:') }}
                 {{ Form::text('win_odds', null, array('class' => 'form-control', 'placeholder' => $selectionprice->win_odds, 'disabled')) }}
             </div>      
            <div class="form-group">
                 {{ Form::label('place_odds', 'Place Odds:') }}
                 {{ Form::text('place_odds', null, array('class' => 'form-control', 'placeholder' => $selectionprice->place_odds, 'disabled')) }}
             </div>

            <div class="form-group">
                {{ Form::label('override_type', 'Override Type:') }}
                {{ Form::select('override_type', array(null => 'No Override', 'percentage' => "Percentage", 'price' => "Price", 'promo' => "Promo"), null, array('class' => 'form-control')) }}
            </div>

            <div class="form-group">
                {{ Form::label('override_odds', 'Override Odds:') }}
                {{ Form::text('override_odds', null, array('class' => 'form-control')) }}
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