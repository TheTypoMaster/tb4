@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Sport Competitions</h2>


                <h2 class="col-lg-4 pull-right">
                    {{ Form::open(array('method' => 'GET')) }}
                    <div class="input-group custom-search-form">
                        {{ Form::text('q', $search, array("class" => "form-control", "placeholder" => "Search (id,name)...")) }}
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                    {{ Form::close() }}
                </h2>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Sport</th>
                    <th>Start Time</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($competitions as $competition)
                    <tr>
                        <td>{{ $competition->id }}</td>
                        <td>{{ $competition->name }}</td>
                        <td>{{ $competition->sport->name }}</td>
                        <td>{{ $competition->start_date }}</td>
                        <td>
                            {{ link_to_route('admin.tournament-sport-markets.edit', "Edit", array($competition->id, "q" => $search), array("class" => "btn btn-warning")) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $competitions->appends(array('q' => $search))->links() }}
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop	