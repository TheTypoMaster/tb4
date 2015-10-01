@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            @if( $user->topbettauser )
                @include('admin.users.partials.header')
            @endif

            {!! Form::model($user, array("route" => array("admin.user-permissions.update", $user->id), "method" => "PUT")) !!}

            <div class="form-group">
                {!! Form::label("permissions[superuser]", "Super User") !!}
                {!! Form::hidden('permissions[superuser]', 0) !!}
                {!! Form::checkbox('permissions[superuser]', 1, $user->isSuperUser()) !!}
            </div>

            <div class="form-group">
                {!! Form::label("groups[]", "Groups") !!}
                {!! Form::select("groups[]", $groups->lists('name', 'id')->all(), $user->groups->lists('id')->all(), array("class" => "form-control select2", "multiple")) !!}
            </div>

            <div class="form-group">
                {!! Form::label("status", $user->block ? "Active the user" : 'Inactive the user') !!}
                {!! Form::checkbox('status', 1, '') !!}
            </div>

            <div class="form-group">
                {!! Form::submit("Save", array("class" => "form-control btn btn-primary")) !!}
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@stop