@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">User Groups</h2>
            </div>

            {{ Form::open(array('route' => 'admin.groups.store', 'method' => 'POST')) }}

            <div class="form-group">
                {{ Form::label('name', "Name: ") }}
                {{ Form::text('name', null, array("class" => 'form-control')) }}
            </div>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Resource</th>
                    <th>View</th>
                    <th>Create</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                </thead>

                <tbody>
                @foreach($resources as $resource)
                </tbody>
            </table>

        </div>
    </div>
@stop