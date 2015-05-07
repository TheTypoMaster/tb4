@extends('layouts.master')

@section('main')
<div class="row">
    <div class="col-lg-12">
        <div class="row page-header">
            <h2 class="col-lg-4">Tournament: <small>{{ $tournament->name }}</small></h2>
        </div>

        {{ Form::open(array("route" => array("/admin/tournaments/add-users", $tournament->id), "method" => "POST")) }}

        <div class="form-group">
            {{ Form::label("users", "Users: ") }}
            {{ Form::textarea("users", null, array("class" => "form-control") }}
        </div>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
@stop