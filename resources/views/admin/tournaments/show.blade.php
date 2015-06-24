@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-6">Tournament <small>{{ $tournament->name }}</small></h2>
            </div>

            <h4>Entrants</h4>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Username</th>
                    <th>Rebuys</th>
                    <th>Topups</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($tournament->tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->user->id }}</td>
                        <td>{{ $ticket->user->username }}</td>
                        <td>{{ $ticket->rebuy_count }}</td>
                        <td>{{ $ticket->topup_count }}</td>
                        <td>
                            @if( ! $tournament->paid_flag )
                                {!! Form::open(array("url" => "/admin/tournaments/remove/" . $tournament->id . "/" . $ticket->user->id , "method" => "POST")) !!}
                                {!! Form::submit('Remove Entrant', array('class' => 'btn btn-danger')) !!}
                                {!! Form::close() !!}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop