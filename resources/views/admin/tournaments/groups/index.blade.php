@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Tournament Groups <small>{{ number_format($groups->total()) }}</small>
                    {!! link_to_route('admin.tournament-groups.create', 'Create', array(), array('class' => 'btn btn-info')) !!}
                </h2>
                <h2 class="col-lg-4 pull-right">
                    {!! Form::open(array('method' => 'GET')) !!}
                    <div class="input-group custom-search-form">
                        {!! Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search (id,name)...")) !!}
                        <span class="input-group-btn">
					<button class="btn btn-default" type="button">
                        <i class="fa fa-search"></i>
                    </button>
				</span>
                    </div>
                    {!! Form::close() !!}
                </h2>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Ordering</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{ $group->id }}</td>
                        <td>{{ $group->group_name }}</td>
                        <td>{{ $group->description }}</td>
                        <td>{{ $group->ordering }}</td>
                        <td>
                            {!! link_to_route('admin.tournament-groups.edit', "Edit", array($group->id, 'q' => $search), array("class" => "btn btn-warning")) !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
        <!-- /.col-lg-12 -->
    </div>

@stop