@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Edit Tournament Event Groups
                    {{--<a href="{{URL::to('event-groups/create')}}"><button class="btn btn-primary">Create</button></a>--}}
                </h2>
            </div>

            @if(isset($event_group_id))
                <?php $default_group_id = $event_group_id;?>
            @endif

            @if(isset($future_meeting_id))
                <?php $default_future_meeting_id = $future_meeting_id;?>
            @endif

            <div class="row" style="margin-left: 20px; margin-right: 20px;">
                {!! Form::open(['url' => 'admin/event-groups/update/'.$event_group_id]) !!}

                <div class="form-group">
                    {!! Form::label('event_group_name', 'Event Group Name: ') !!}
                    {!! Form::input('text', 'event_group_name', $event_group->name, array('class' => 'form-control')) !!}
                </div>

                @if($flag == 'existing_meeting')
                    <div class="form-group">
                        {!! Form::label('sports', 'Sports: ') !!}
                        {!! Form::select('sports', $sport_list, [], array('id' => 'sports', 'class' => 'form-control', 'placeholder' => '--Select a sport--')) !!}
                    </div>


                    <div class="form-group">
                        {!! Form::label('event_groups', 'Event Groups: ') !!}
                        {!! Form::select('event_groups', [], [], array('id' => 'event_groups', 'class' => 'form-control', 'placeholder' => '--Select an event group--')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('events', 'Events: ') !!}
                        {!! Form::select('events[]', [], [], array('id' => 'events', 'class' => 'form-control select2', 'multiple')) !!}
                    </div>
                @else
                    <div class="form-group" id="race_form">
                        {!! Form::label('races', 'Race: ') !!}
                        {!! Form::select('races', ['1' => 'galloping', '2' => 'harness', '3' => 'greyhounds'], [], array('id' => 'races', 'class' => 'form-control')) !!}
                    </div>

                    <div class="form-group" id="meeting_form">
                        {!! Form::label('meeting', 'Future Meeting ') !!}

                        <input type="text"  id="search_meeting" class="" placeholder="Keyword..." style="margin-left: 20px;"><span id="search_meeting_button" class=""><button class="btn btn-default" type="button">Search</button></span>
                        {!! Form::select('meeting', [], [], array('id' => 'meeting', 'class' => 'form-control')) !!}
                    </div>

                    <div class="form-group" id="meeting_date">
                        {!! Form::label('meeting_date', 'Future Meeting Start') !!}
                        {{--{!! Form::input('date', 'meeting_date', \Carbon\Carbon::now()->format('Y-m-d'), array('class' => 'form-control')) !!}--}}
                        {!! Form::datetime('meeting_date', null, array("class"=>"event-date datepicker")) !!}
                    </div>
                @endif


                {!! Form::input('hidden', 'flag', $flag, array('id' => 'flag')) !!}


                <div class="form-group">
                    {!! Form::submit('Update', array('class' => 'btn btn-primary')) !!}
                </div>

                {!! Form::input('hidden', 'event_group_id', $default_group_id, array()) !!}
                {!! Form::input('hidden', 'competition_id', $default_future_meeting_id, array()) !!}

                {!! Form::close() !!}
            </div>

            @if($flag == 'existing_meeting')
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
                                @if($event->number != null)
                                    <td>(#{{$event->id}}, Race: {{$event->number}}) {{$event->name}}</td>
                                @else
                                    <td>(#{{$event->id}}) {{$event->name}}</td>
                                @endif
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
            @endif

            @if (count($errors) > 0)
                <div class="alert alert-info alert-dismissable col-lg-11">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

            function createSelectOptionsForEvents(json) {
                var html = $();
                var race_number = '';

                $.each(json, function (index, value) {

                    if(value.number != null) {
                        race_number = ', Race: '+value.number;
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
                            $('#event_groups').html(createSelectOptions(data));
                            $('#event_groups').change();
                            $('#events').empty();
                            $('#events').select2({
                                placeholder: '',
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
        });

    </script>

    <script type="text/javascript">
        $(".datepicker").datetimepicker({format: 'YYYY-MM-DD HH:mm'});
    </script>

@stop