@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Tournament Settings</h2>
            </div>

            {!! Form::open(array("method" => "PUT")) !!}
            @if( $freeLimit = array_get($config, 'max_free_tournament', null) )
                <fieldset>
                    <legend>Max. Free Tournament Buyins</legend>

                    <div class="form-group">
                        {!! Form::label('max_free_tournament[period]', 'Period') !!}
                        {!! Form::select('max_free_tournament[period]', \TopBetta\Services\Tournaments\TournamentBuyInRulesService::$freeTournamentPeriods, array_get($config, 'max_free_tournament.period', null), array('class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('max_free_tournament[number]', 'Number') !!}
                        {!! Form::number('max_free_tournament[number]', array_get($config, 'max_free_tournament.number', null), array('class' => 'form-control')) !!}
                    </div>
                </fieldset>
            @endif

            <div class="form-group">
                {!! Form::submit('Save', array('class' => "form-control btn btn-primary")) !!}
            </div>
            {!! Form::close() !!}

        </div>
    </div>
@stop