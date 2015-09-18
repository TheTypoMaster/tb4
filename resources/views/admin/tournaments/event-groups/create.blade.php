@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Tournament Event Groups
                    {{--<a href="{{URL::to('event-groups/create')}}"><button class="btn btn-primary">Create</button></a>--}}
                </h2>
            </div>

            <div class="row" style="margin-left: 20px; margin-right: 20px;">
                {!! Form::open(['url' => 'admin/event-groups/store']) !!}

                <div class="form-group">
                    {!! Form::label('event_group_name', 'Event Group Name: ') !!}
                    {!! Form::input('text', 'event_group_name', null, array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('events', 'Events: ') !!}
                    {!! Form::select('events[]', $event_group_list, [], array('class' => 'form-control', 'multiple')) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Submit', array('class' => 'btn btn-primary')) !!}
                </div>

                {!! Form::close() !!}
            </div>

        </div>
    </div>

@stop