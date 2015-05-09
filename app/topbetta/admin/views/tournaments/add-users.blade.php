@extends('layouts.master')

@section('main')
<div class="row">
    <div class="col-lg-12">
        <div class="row page-header">
            <h2 class="col-lg-6">Tournament: <small>{{ $tournament->name }}</small></h2>
        </div>

        @if(isset($result) && count($result))
            <h4>Entered</h4>
            <ul>
                @foreach($result['entered'] as $enteredUser)
                    <li>{{ $enteredUser['username'] }}</li>
                @endforeach
            </ul>

            <h4>Not Entered</h4>
            <ul>
                @foreach($result['not_entered'] as $notEnteredUser)
                    <li>{{ $notEnteredUser['username'] }}, reason: {{ $notEnteredUser['reason'] }}</li>
                @endforeach
            </ul>
        @endif

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