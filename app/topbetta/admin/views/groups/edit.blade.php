@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">User Groups</h2>
            </div>

            {{ Form::model($group, array('route' => array('admin.groups.update', $group->id), 'method' => 'PUT')) }}

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
                    <tr>
                        <td>{{ $resource['name'] }}</td>
                        @foreach( Config::get('adminresources.permissions') as $permission )
                            @if( ! array_get($resource, 'only', null) || in_array($permission, $resource['only']) )
                                <td class="col-xs-1">
                                    {{ Form::select("permissions[". Config::get('adminresources.prefix') . '.' . $resource['name'] . '.' . $permission ."]", array(
                                        0 => "No",
                                        1 => "Yes"
                                    ),
                                    array_get($group->permissions, Config::get('adminresources.prefix') . '.' . $resource['name'] . '.' . $permission, 0),
                                    array("class" => "form-control")) }}
                                </td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="form-group">
                {{ Form::submit('Save', array('class' => 'form-control btn btn-primary')) }}
            </div>

        </div>
    </div>
@stop