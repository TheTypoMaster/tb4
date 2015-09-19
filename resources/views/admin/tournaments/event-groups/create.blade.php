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
            ?>

            @if(isset($event_group_name))
                <?php $default_group_name = $event_group_name;?>
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
                    {!! Form::select('event_groups', $event_group_list, [], array('id' => 'event_groups', 'class' => 'form-control', 'placeholder' => '--Select an event group--')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('events', 'Events ') !!}
                    {!! Form::select('events[]', $event_group_list, [], array('id' => 'events', 'class' => 'select2 form-control', 'placeholder' => '--Select events--', 'multiple')) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Add', array('class' => 'btn btn-primary')) !!}
                </div>

                {!! Form::input('hidden', 'event_group_id', $default_group_id, array()) !!}

                {!! Form::close() !!}
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