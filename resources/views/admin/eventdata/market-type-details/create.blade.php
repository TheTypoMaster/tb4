@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Market Type Details
                    {!! link_to_route('admin.market-type-details.create', 'Create', array(), array('class' => 'btn btn-info')) !!}
                </h2>
            </div>

            {!! Form::open(array('route' => 'admin.market-type-details.store', 'method' => 'POST')) !!}

            <div class="form-group">
                {!! Form::label("sport_id", 'Sport') !!}
                {!! Form::select("sport_id", $sports->lists('name','id')->all(), null, array('class'=>'form-control')) !!}
            </div>

            <div class="form-group">
                {!! Form::label("market_type_id", 'Market Type') !!}
                {!! Form::select("market_type_id", $marketTypes->lists('name','id')->all(), null, array('class' => 'form-control select2')) !!}
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