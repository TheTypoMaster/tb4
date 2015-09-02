@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Market Type Details</h2>
            </div>

            {!! Form::model($detail, array('route' => array('admin.market-type-details.update', $detail->id), 'method' => 'PUT')) !!}

            <div class="form-group">
                {!! Form::label("sport_id", 'Sport') !!}
                {!! Form::select("sport_id", array($detail->sport_id => $detail->sport->name), null, array('class'=>'form-control', 'disabled')) !!}
            </div>

            <div class="form-group">
                {!! Form::label("market_type_id", 'Market Type') !!}
                {!! Form::select("market_type_id", array($detail->market_type_id => $detail->marketType->name), null, array('class' => 'form-control', 'disabled')) !!}
            </div>

            <div class="form-group">
                {!! Form::label("max_winning_selections", "Maximum winning selctions") !!}
                {!! Form::number('max_winning_selections', null, array('class' => 'form-control')) !!}
            </div>

            <div class="form-group">
                {!! Form::submit('Save', array('class' => 'form-control btn btn-primary')) !!}
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@stop