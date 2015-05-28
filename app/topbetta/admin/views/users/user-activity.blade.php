@extends('layouts.master')

@section('main')

    <div class="row">
        <div class="page-header">
            <h2>User Activity</h2>
        </div>

        <p>
            Required CSV Format: first name, last name, DOB <br/><small>e.g. John, Smith, 01/01/2001</small>
        </p>

        {{ Form::open(array('url' => 'admin/user-activity/download', 'method' => "POST", "files" => true)) }}

        <div class="form-group">
            {{ Form::label("users", "Users CSV: ") }}
            {{ Form::file("users") }}
        </div>

        <div class="form-group">
            {{ Form::submit("Download", array("class" => "form-control btn btn-primary")) }}
        </div>

        {{ Form::close() }}
    </div>

@stop