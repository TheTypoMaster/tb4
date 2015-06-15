@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">User Groups <span>{{ link_to_route("admin.groups.create", "Create", array(), array("class" => "btn btn-primary")) }}</span></h2>
            </div>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{ $group->id }}</td>
                        <td>{{ $group->name }}</td>
                        <td>
                            {{ link_to_route('admin.groups.edit', "Edit", array($group->id), array("class" => "btn btn-warning")) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
@stop