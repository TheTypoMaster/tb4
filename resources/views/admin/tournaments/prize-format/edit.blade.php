@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Edit Prize Format
                </h2>
            </div>

            <div class="row" style="margin-left: 20px; margin-right: 20px;">
                {!! Form::open(['url' => 'admin/prize-format/update/'.$prize_format->id]) !!}
                <div class="form-group event-group-container" id="event-group">
                    {!! Form::label('short_name', 'Short Name') !!}<br/>
                    {!! Form::input('text', 'short_name', $prize_format->short_name, array("class" => "form-control")) !!}
                </div>

                <div class="form-group event-group-container" id="event-group">
                    {!! Form::label('icon', 'Icon') !!}<br/>
                    {!! Form::input('text', 'icon', $prize_format->icon, array("class" => "form-control")) !!}
                </div>

                <div class="form-group event-group-container" id="event-group">
                    {!! Form::submit('Update', array('class' => 'btn btn-primary')) !!}
                </div>

                {!! Form::close() !!}

            </div>

        </div>
    </div>

@stop