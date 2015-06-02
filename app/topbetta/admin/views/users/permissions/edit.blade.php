@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            @include('admin::users.partials.header')
            <h4 class="page-header">Permissions</h4>

            {{ Form::model($user, array("route" => array("admin.user-permissions.update", $user->id), "method" => "PUT")) }}

            <div class="form-group">
                {{ Form::label("permissions[superuser]", "Super User") }}
                {{ Form::checkbox('permissions[superuser]', null) }}
            </div>

            <div class="form-group">
                {{ Form::label("groups[]", "Groups") }}
                {{ Form::select("groups[]", $groups->lists('name', 'id'), null, array("class" => "form-control select2", "multiple")) }}
            </div>

            <div class="form-group">
                {{ Form::submit("Save", array("class" => "form-control btn btn-primary")) }}
            </div>
        </div>
    </div>
@stop