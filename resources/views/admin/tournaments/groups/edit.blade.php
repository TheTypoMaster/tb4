@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Tournament Groups</h2>
            </div>
        </div>

        {!! Form::model($group, array('route' => array('admin.tournament-groups.update', $group->id, 'q' => $search), 'method' => 'PUT')) !!}
            <div class="form-group">
                {!! Form::label('group_name', 'Name') !!}
                {!! Form::text('group_name', null, array('class' => 'form-control')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('description' , 'Description') !!}
                {!! Form::textarea('description', null, array('class' => 'form-control')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('ordering', 'Ordering') !!}
                {!! Form::number('ordering', null, array('class' => "form-control")) !!}
            </div>

            <div class="form-group">
                {!! Form::submit('Save', array('class' => 'form-control btn btn-primary')) !!}
            </div>
        {!! Form::close() !!}

    </div>

@stop