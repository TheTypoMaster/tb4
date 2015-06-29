@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Event Results</h2>

                <h2 class="col-lg-4 pull-right">
                    {!! Form::open(array('method' => 'GET')) !!}
                    <div class="input-group custom-search-form">
                        {!! Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search (id,name)...")) !!}
                        <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                    </div>
                    {!! Form::close() !!}
                </h2>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Sport</th>
                    <th>Competition</th>
                    <th>Event</th>
                    <th>Start Time</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($events as $event)
                    <tr>
                        <td>{{ $event->competition->first()->sport->name }}</td>
                        <td>{{ $event->competition->first()->name }}</td>
                        <td>{{ $event->name }}</td>
                        <td>{{ $event->start_date }}</td>
                        <td>
                            {!! link_to_route('admin.tournament-sport-results.edit', $event->isPaying() ? "View" : "Result", array($event->id, 'q' => $search), array("class" => "btn btn-warning")) !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $events->appends(array('q' => $search))->render() !!}
        </div>
    </div>
@stop