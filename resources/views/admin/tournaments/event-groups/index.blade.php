@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Tournament Event Groups
                <a href="{{URL::to('admin/event-groups/create')}}"><button class="btn btn-primary">Create</button></a>
                </h2>

            </div>

            <div class="row pull-right" style="margin-left: 20px; margin-right: 20px;">
                {!! Form::open(['url' => 'admin/event-groups']) !!}
                {!! Form::label('search_username', 'Search by name: ') !!}
                {!! Form::input('text', 'search_username', '', array('id' => 'search_username', 'placeholder' => 'keywords...')) !!}
                {!! Form::submit('Search') !!}
                {!! Form::close() !!}
            </div>

            <div class="row">
                <table class="table" style="margin-left: 20px; margin-right: 20px;">
                    <tr>
                        <th>Event Group ID</th>
                        <th>Event Group Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th colspan="2" align="centre">Action</th>
                    </tr>

                    @foreach($event_groups as $event_group)
                        <tr class="filter">
                            <td>{{$event_group->id}}</td>
                            <td class="username">{{$event_group->name}}</td>
                            <td>{{$event_group->start_date}}</td>
                            <td>{{$event_group->end_date}}</td>
                            <td><a href="{{URL::to('admin/event-groups/edit/' . $event_group->id)}}"><button class="btn btn-primary">Edit</button></a></td>
                            <td><a href="{{URL::to('admin/event-groups/delete/' . $event_group->id)}}"><button class="btn btn-primary">Delete</button></a></td>
                        </tr>
                    @endforeach
                </table>

                {{-- add pagination --}}
                {!! $event_groups->render() !!}

            </div>

        </div>
    </div>

@stop