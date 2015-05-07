@extends('layouts.master')

@section('main')
<div class="row">
    <div class="col-lg-12">
        <div class="row page-header">
            <h2 class="col-lg-6">Tournament: <small>{{ $tournament->name }}</small></h2>
        </div>

        {{ Form::open(array("url" => array("/admin/tournaments/add-users", $tournament->id), "method" => "POST")) }}

        <div class="form-group">
            {{ Form::label("users", "Users: ") }}
            {{ Form::textarea("users", null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::submit("Save", array("class" => "btn btn-primary form-control")) }}
        </div>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop