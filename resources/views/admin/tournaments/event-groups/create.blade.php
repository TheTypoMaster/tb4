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

                <a id="select_future_meeting">Select Future Meeting</a>
                <div class="form-group" id="event_group_form">
                    {!! Form::label('event_groups', 'Event Groups: ') !!}
                        <input type="text"  id="search" class="" placeholder="Keyword..." style="margin-left: 20px;"><span id="search_button" class=""><button class="btn btn-default" type="button">Search</button></span>

                    {{--<a><span id="search_label" style="margin-left: 20px;">Search </span></a>--}}
                    {{--<input type="text" name="search", id="search", class="hidden">--}}
                    {!! Form::select('event_groups', [], [], array('id' => 'event_groups', 'class' => 'form-control', 'placeholder' => '--Select an event group--')) !!}
                </div>

                <div class="form-group hidden" id="meeting_form">
                    {!! Form::label('meeting', 'Future Meeting ') !!}

                    <input type="text"  id="search_meeting" class="" placeholder="Keyword..." style="margin-left: 20px;"><span id="search_meeting_button" class=""><button class="btn btn-default" type="button">Search</button></span>
                    {!! Form::select('meeting', [], [], array('id' => 'meeting', 'class' => 'form-control')) !!}
                </div>

                <div class="form-group hidden" id="meeting_date">
                    {!! Form::label('meeting_date', 'Future Meeting Start') !!}
                    {{--{!! Form::input('date', 'meeting_date', \Carbon\Carbon::now()->format('Y-m-d'), array('class' => 'form-control')) !!}--}}
                    {!! Form::datetime('meeting_date', null, array("class"=>"event-date datepicker")) !!}
                </div>

                {{-- the flag is used for controller to process different kinds of data --}}
                {!! Form::input('hidden', 'flag', 'existing_meeting', array('id' => 'flag')) !!}

                <div class="form-group" id="events-form">
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
                            <th>Event Group Name:</th>
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

                                @if($event->number != null)
                                    <td>(#{{$event->id}}, Race: {{$event->number}}) {{$event->name}}</td>
                                @else
                                    <td>(#{{$event->id}}) {{$event->name}}</td>
                                @endif

                                <td>{{$event->start_date}}</td>
                                <td>
                                    <a href="{{URL::to('admin/event-groups/remove_event/' . $event_group_id . '/' . $event->id . '/' . $event_group_name)}}">
                                        <button class="btn btn-primary">Remove</button>
                                    </a></td>
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


            var event_group_data = '';
            var meetings = '';

            function createSelectOptions(json) {
                var html = $();

                $.each(json, function (index, value) {
//                if ($.inArray(value.name, ['Select Competition', 'Select Sport', 'Select Event']) < 0) {
//                    html = html.add($
//                    ('<option></option>').text(value.name).val(value.id));
//                }
                    html = html.add($
                    ('<option></option>').text('(#' + value.id + ') ' + value.name + ' ------Start at: ' + value.start_date).val(value.id));
                });

                return html;
            }

            function createSelectOptionsForEvents(json) {
                var html = $();
                var race_number = '';

                $.each(json, function (index, value) {

                    if (value.number != null) {
                        race_number = ', Race: ' + value.number;
                    }

                    html = html.add($
                    ('<option></option>').text('(#' + value.id + '' + race_number + ') ' + value.name + ' '+value.start_date).val(value.id));
                });

                return html;
            }

            function createSelectOptionsForMeetings(json) {
                var html = $();

                $.each(json, function (index, value) {
//                if ($.inArray(value.name, ['Select Competition', 'Select Sport', 'Select Event']) < 0) {
//                    html = html.add($
//                    ('<option></option>').text(value.name).val(value.id));
//                }
                    html = html.add($
                    ('<option></option>').text('(#' + value.id + ') ' + value.name).val(value.id));
                });

                return html;
            }

            //get all meetings
            $.get('/admin/get-meetings')
                    .done(function(data) {
                        $('#meeting').html(createSelectOptionsForMeetings(data));
                        $('#meeting').change();
                        meetings = data;
                    });

            $('#sports').change(function () {
                var sport = $('#sports').val();

                $.get('/admin/get-event-groups/' + sport)
                        .done(function (data) {
                            event_group_data = data;
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
                            $('#events').html(createSelectOptionsForEvents(data));

                            $('#events').select2({
                                placeholder: '--Select events--',
                                closeOnSelect: false,
                                multiple: true
                            });
                        });
            });

            $('#search_label').click(function() {
                $('#search').removeClass('hidden');
            });

            $('#search_button').click(function() {

                    var search_list = new Array();
                    for(var i=0; i<event_group_data.length; i++) {
                        var search_value = $('#search').val();
                        if(event_group_data[i].name.toLowerCase().indexOf(search_value.toLowerCase()) >= 0) {

                            search_list.push(event_group_data[i]);

                        }

                    }

                $('#event_groups').empty();
                $('#event_groups').html(createSelectOptions(search_list));
                $('#event_groups').change();
            });

            $('#select_future_meeting').click(function() {

                $('#meeting_form').toggleClass('hidden');
                $('#event_group_form').toggleClass('hidden');
                $('#events-form').toggleClass('hidden');
                $('#meeting_date').toggleClass('hidden');

                //change flag
                if($('#events-form').hasClass('hidden')) {
                    $('#flag').val('future_meeting');
                } else {
                    $('$flag').val('existing_meeting');
                }

                if($('#select_future_meeting').text() == 'Select Future Meeting') {
                    $('#select_future_meeting').text('Select Existing Meeting');
                } else {
                    $('#select_future_meeting').text('Select Future Meeting');
                }


            });

            $('#search_meeting_button').click(function() {

                var search_list = new Array();
                for(var i=0; i<meetings.length; i++) {
                    var search_value = $('#search_meeting').val();
                    if(meetings[i].name.toLowerCase().indexOf(search_value.toLowerCase()) >= 0) {

                        search_list.push(meetings[i]);

                    }

                }

                $('#meeting').empty();
                $('#meeting').html(createSelectOptionsForMeetings(search_list));
                $('#meeting').change();
            });

        });

    </script>

    <script type="text/javascript">
        $(".datepicker").datetimepicker({format: 'YYYY-MM-DD HH:mm'});
    </script>

@stop