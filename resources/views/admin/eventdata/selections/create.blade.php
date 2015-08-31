@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Create Selection

            </h2>
            <ul class="nav nav-tabs">
                <span class='pull-right'>{!! link_to_route('admin.markets.index', 'Back to Markets', array('event' => $event, 'q'=>$search), array('class' => 'btn btn-outline btn-warning')) !!}</span>
            </ul>
            <div class='col-lg-6'>
                {!! Form::open(array('method' => 'POST', 'route' => array('admin.selections.store', 'event' => $event, 'market' => $market->id, "q" => $search))) !!}
                <div class="form-group">
                    {!! Form::label('event_name', 'Event Name:') !!}
                    {!! Form::text('event_name', null, array('class' => 'form-control', 'placeholder' => $market->event->name, 'disabled')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('market_name', 'Market:') !!}
                    {!! Form::text('market_name', $market->marketType->name, array('class' => 'form-control', 'disabled')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('name', 'Selection Name:') !!}
                    {!! Form::text('name', null, array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('team', 'Team:') !!}
                    {!! Form::select('team[]', $teams, null, array('class' => 'form-control select2')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('player', 'Player:') !!}
                    {!! Form::select('player[]', $players, null, array('class' => 'form-control select2')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('selection_status_id', 'Selection Status:') !!}
                    {!! Form::select('selection_status_id', array(
                        '1' => 'Not Scratched',
                        '2' => 'Scratched',
                        '3' => 'Late Scratching',
                        '4' => 'Suspended'), null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('display_flag', "Betting Open on TopBetta: ") !!}
                    {!! Form::select('display_flag', array(0 => "No", 1 => "Yes"), 1, array("class" => "form-control")) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('win_dividend', "Win Dividend") !!}
                    {!! Form::text('win_dividend', null, array('class' => 'form-control')); !!}
                </div>
                <div class="form-group">
                    {!! Form::label('place_dividend', "Place Dividend (racing only)") !!}
                    {!! Form::text('place_dividend', null, array('class' => "form-control")) !!}
                </div>
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
    <!-- /.row -->
@stop