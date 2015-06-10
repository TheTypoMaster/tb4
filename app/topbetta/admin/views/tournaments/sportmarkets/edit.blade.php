@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Sport Competitions</h2>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <h4 class="page-header">Competition Data</h4>
                    <dl class="dl-horizontal">
                        <dt>Sport</dt>
                        <dd>{{ $competition->sport->name }}</dd>

                        <dt>Competition Name</dt>
                        <dd>{{ $competition->name }}</dd>
                    </dl>
                </div>

                <div class="col-lg-6">
                    <h4 class="page-header">Events</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Start Date</th>
                        </tr>
                        </thead>

                        @foreach($competition->events()->get() as $event)
                            <tr>
                                <td>{{ $event->name }}</td>
                                <td>{{ $event->start_date }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="row">
                {{ Form::open(array("route" => array("admin.tournament-sport-markets.update", $competition->id, "q" => $search), 'method' => "PUT")) }}

                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Market</th>
                        <th>Line</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($markets as $market)
                        <tr>
                            <td>{{ $market->marketType->name }}</td>
                            <td>{{ $market->line }}</td>
                            <td>{{ $market->marketType->description }}</td>
                            <td>
                                {{ Form::checkbox('market_types[]', $market->marketType->id, in_array($market->marketType->id, $competition->tournament_market_types->lists('id'))) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="form-group">
                    {{ Form::submit("Save", array("class" => "form-control btn btn-primary")) }}
                </div>

            </div>
        </div>
    </div>
@stop