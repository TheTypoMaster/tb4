@extends('layouts.master')

@section('main')
<div class="row">
    <div class="col-lg-12">
        <div class="row page-header">
            <h2 class="col-lg-4">Remove Credits From Dormant Accounts</h2>
        </div>

        {{ Form::open(array('method' => 'GET', "class" => "form-inline", "url" => "/admin/removeFreeCredits")) }}

        <div class="form-group">
            {{ Form::label('days', "Dormant for how many days?")}}
            {{ Form::number('days', $defaultDays, array("class" => "form-control", "placeholder" => "Days")) }}

        </div>
        <div class="form-group">
            {{ Form::submit("Remove", array("class" => "btn btn-danger")) }}
        </div>
        {{ Form::close() }}
    </div>
</div>
@stop