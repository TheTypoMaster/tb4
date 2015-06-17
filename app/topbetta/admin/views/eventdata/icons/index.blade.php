@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Icons
                    <a href="{{route('admin.icons.create', array("q" => $search))}}" class="btn btn-primary">Create <i class="glyphicon glyphicon-plus"></i></a>
                </h2>

                <h2 class="col-lg-4 pull-right">
                {{ Form::open(array('method' => 'GET')) }}
                <div class="input-group custom-search-form">
                    {{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search...")) }}
                    <span class="input-group-btn">
        					<button class="btn btn-default" type="button">
                                <i class="fa fa-search"></i>
                            </button>
        				</span>
                </div>
                {{ Form::close() }}
                </h2>
            </div>

            <table class="table table-striped">
                <tr>
                    <th>Id</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th></th>
                </tr>

                @foreach($icons as $icon)
                    <tr>
                        <td>{{ $icon->id }}</td>
                        <td><img src="{{ $icon->icon_url }}" style="width:25px;height:25px;"/></td>
                        <td>{{ $icon->name }}</td>
                        <td>{{ $icon->iconType->name }}</td>
                        <td>
                            {{ Form::open(array("class" => "form form-inline", "method" => "DELETE", "route" => array('admin.icons.destroy', $icon->id, 'q' => $search))) }}
                                <a href="{{ route('admin.icons.edit', array($icon->id, 'q' => $search)) }}" class="btn btn-warning btn-small"><i class="glyphicon glyphicon-edit"></i></a>
                                <button type="submit" class="btn btn-danger btn-small delete-button"><i class="glyphicon glyphicon-remove"></i></button>
                            {{ Form::close() }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop