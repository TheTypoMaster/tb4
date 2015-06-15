@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Event Results <small>{{ $event->name }}</small></h2>
            </div>

            {{ Form::open(array('route' => array('admin.tournament-sport-results.update', $event->id, 'q' => $search), 'method' => "PUT")) }}

            @foreach($tournamentMarkets as $market)
                <div class="form-group">
                    {{ Form::label('market_results[]', $market->marketType->name) }}
                    {{ Form::select("market_results[" . $market->id ."]", array(0 => "Refund/Cancel") + $market->selections->lists('name', 'id'), $market->result->count() ? $market->result->selection_id : null, array("class" => "form-control")); }}
                </div>
            @endforeach

            <div class="form-group">
                {{ Form::submit("Save", array("class" => "form-control btn btn-primary", $eventPaying ? 'disabled' : null)) }}
            </div>
        </div>
    </div>
@stop