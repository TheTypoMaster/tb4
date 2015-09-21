@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Tournament Event Groups
                    {{--<a href="{{URL::to('event-groups/create')}}"><button class="btn btn-primary">Create</button></a>--}}
                </h2>
            </div>

            <?php

            $default_group_name = '';
            $default_group_id = '';
            $disable = '';
            ?>

            @if(isset($event_group_name))
                <?php
                    $default_group_name = $event_group_name;
                    $disable = 'disabled';
                ?>
            @endif

            @if(isset($event_group_id))
                <?php $default_group_id = $event_group_id;?>
            @endif

            <div class="row" style="margin-left: 20px; margin-right: 20px;">
                {!! Form::open(['url' => 'admin/event-groups/store?XDEBUG_SESSION_START']) !!}

                <div class="form-group">
                    {!! Form::label('event_group_name', 'Event Group Name: ') !!}
                    {!! Form::input('text', 'event_group_name', $default_group_name, array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('sports', 'Sports: ') !!}
                    {!! Form::select('sports', $sport_list, [], array('id' => 'sports', 'class' => 'form-control', 'placeholder' => '--Select a sport--')) !!}
                </div>


                <div class="form-group">
                    {!! Form::label('event_groups', 'Event Groups: ') !!}
                    {!! Form::select('event_groups', [], [], array('id' => 'event_groups', 'class' => 'form-control', 'placeholder' => '--Select an event group--')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('events', 'Events ') !!}
                    {!! Form::select('events[]', [], [], array('id' => 'events', 'class' => 'select2 form-control', 'placeholder' => '--Select events--', 'multiple')) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Add', array('class' => 'btn btn-primary')) !!}
                </div>

                {!! Form::input('hidden', 'event_group_id', $default_group_id, array()) !!}

                {!! Form::close() !!}
            </div>

            <div class="row" style="margin-left: 20px; margin-top: 40px; width: 60%;">
                @if(isset($event_list))
                    <table class="table">
                        <tr>
                            <th>Event Group Name: </th>
                            <th>Events</th>
                            <th>Start Date</th>
                            <th>Action</th>
                        </tr>

                        @foreach($event_list as $id => $event_with_group_name)
                            <?php
                                $event = $event_with_group_name['event'];
                            ?>
                            <tr>
                                <td>{{$event_with_group_name['event_group_name']}}</td>
                                <td>(#{{$event->id}}) {{$event->name}}</td>
                                <td>{{$event->start_date}}</td>
                                <td><a href="{{URL::to('admin/event-groups/remove_event/' . $event_group_id . '/' . $event->id . '/' . $event_group_name)}}"><button class="btn btn-primary">Remove</button></a></td>
                            </tr>
                        @endforeach
                    </table>

                    <a href="{{URL::to('admin/event-groups')}}">
                        <button class="btn btn-primary">Done</button>
                    </a>
                @endif

            </div>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#events').select2({
                placeholder: 'select'
            });


            function createSelectOptions(json) {
                var html = $();

                $.each(json, function (index, value) {
//                if ($.inArray(value.name, ['Select Competition', 'Select Sport', 'Select Event']) < 0) {
//                    html = html.add($
//                    ('<option></option>').text(value.name).val(value.id));
//                }
                    html = html.add($
                    ('<option></option>').text(value.name).val(value.id));
                });

                return html;
            }

            $('#sports').change(function () {
                var sport = $('#sports').val();

                $.get('/admin/get-event-groups/' + sport)
                        .done(function (data) {
                            $('#event_groups').html(createSelectOptions(data));
                            $('#event_groups').change();
                            $('#events').empty();
                            $('#events').select2({
                                placeholder: ''
                            });
                        });
            });

            $('#event_groups').change(function () {
                var event_group = $('#event_groups').val();

                $.get('/admin/get-events/' + event_group)
                        .done(function (data) {
                            $('#events').html(createSelectOptions(data));

                            $('#events').select2({
                                placeholder: '--Select events--'
                            });
                        });
            });
        });

    </script>

@stop