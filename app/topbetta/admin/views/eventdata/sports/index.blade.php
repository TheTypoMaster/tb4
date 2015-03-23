@extends('layouts.master')

@section('main')

@include('admin::eventdata.partials.templates.index-template', array(
    "modelName" => "Sports",
    "modelCollection" => $sports,
    "editRoute" => "admin.sports.edit",
    "deleteRoute" => "admin.sports.destroy",
    "extraFields" => array(
        "Default Competition Icon" => array(
            "type" => "image",
            "field" => "defaultCompetitionIcon.icon_url"
        ),
    ),
    "search" => $search,
));

@stop