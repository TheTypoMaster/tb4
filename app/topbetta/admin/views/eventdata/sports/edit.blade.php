@extends('layouts.master')

@section('main')

    @include('admin::eventdata.partials.templates.form-template', array(
        "model" => $sport,
        "modelName" => "Sport",
        "returnRoute" => "admin.sports.index",
        "updateRoute" => "admin.sports.update",
        "extraFields" => array(),
        "icons" => $icons,
    ))
@stop