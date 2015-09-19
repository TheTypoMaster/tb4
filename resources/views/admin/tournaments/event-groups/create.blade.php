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
                {!! Form::open(['url' => 'admin/event-groups/store?XDEBUG_SESSION_START']) !!}

                <div class="form-group">
                    {!! Form::label('event_group_name', 'Event Group Name: ') !!}
                    {!! Form::input('text', 'event_group_name', null, array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('sports', 'Sports: ') !!}
                    {!! Form::select('sports', $sport_list, [], array('id' => 'sports', 'class' => 'form-control', 'placeholder' => '--Select a sport--')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('event_groups', 'Event Groups: ') !!}
                    {!! Form::select('event_groups', $event_group_list, [], array('id' => 'event_groups', 'class' => 'form-control', 'placeholder' => '--Select an event group--')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('events', 'Events ') !!}
                    {!! Form::select('events', $event_group_list, [], array('id' => 'events', 'class' => 'form-control', 'placeholder' => '--Select events--')) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Submit', array('class' => 'btn btn-primary')) !!}
                </div>

                {!! Form::close() !!}
            </div>

        </div>
    </div>

    <script>
        //        $(document).ready(function () {
        //            $('#events').select2({
        //                placeholder: 'select'
        //            });
        //        });

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
                    });
        });

        $('#event_groups').change(function () {
            var event_group = $('#event_groups').val();

            $.get('/admin/get-events/' + event_group)
                    .done(function (data) {
                        $('#events').html(createSelectOptions(data));
                    });
        });
    </script>

@stop