@extends('layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">

            <h1>Icons</h1>

            <div class="col-md-6">
                {{ Form::model($icon, array("route" => array("admin.icons.update", $icon->id, 'q' => $search), "method" => "PUT", "files" => true)) }}

                <div class="form-group">
                    {{ Form::label("name", "Name: ") }}
                    {{ Form::text("name", null, array("class" => "form-control")) }}
                </div>

                <div class="form-group" >
                    {{ Form::label("icon_file", "Image: ") }}
                    <img src="{{ $icon->icon_url }}" style="width:50px; height:50px" />
                </div>

                <div class="form-group" >
                    {{ Form::label("icon_type_id", "Icon Type: ") }}
                    {{ Form::select("icon_type_id", $iconTypes->lists('name', 'id'), null, array("class" => "form-control")) }}
                </div>

                <div class="col-lg-12">
                    <div class="form-group">
                        {{ Form::submit('Save', array('class' => 'btn btn-info')) }}
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop