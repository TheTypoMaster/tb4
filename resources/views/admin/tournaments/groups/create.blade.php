@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Tournament Groups</h2>
            </div>

            {!! Form::open(array('route' => array('admin.tournament-groups.store', 'q' => $search), 'method' => 'POST')) !!}
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
    </div>
@stop