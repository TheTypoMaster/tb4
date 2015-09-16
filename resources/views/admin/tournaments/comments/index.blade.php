@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">

            </div>

            <div class="row">
            <div class="pull-right" style="margin-right: 60px; margin-bottom: 20px;">
                {!! Form::open(['url' => 'admin/tournament-comments/filter']) !!}
                {!! Form::label('tournament_id', 'Tournament ID.: ') !!}
                {!! Form::input('text', 'tournament_id', '') !!}
                {!! Form::label('username', 'Username: ', '') !!}
                {!! Form::input('text', 'username', '') !!}
                {!! Form::label('visible', 'Visible Only: ') !!}
                {!! Form::checkbox('visible', 1, false) !!}
                {!! Form::submit('Filter', ['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
            </div>
            </div>
            <div class="row">
            <div class="pull-right" style="margin-right: 60px; margin-bottom: 20px;">
                {!! Form::open(['url' => 'admin/tournament-comments/store']) !!}
                {!! Form::label('new_comment', 'Add Comment: ') !!}
                {!! Form::input('text', 'new_comment', '') !!}
                {!! Form::select('tournament', $tournament_list) !!}
                {!! Form::submit('Add', ['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
            </div>
                </div>
            <div class="row">
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Tournament Id</th>
                        <th>Tournament Name</th>
                        <th>Buy In</th>
                        <th>Entry Fee</th>
                        <th>Time of Comment</th>
                        <th>Visible</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                    @foreach($comments as $key => $comment)
                        <tr>
                            <td>{{$comment['id']}}</td>
                            <td>{{$comment['username']}}</td>
                            <td>{{$comment['tournament_id']}}</td>
                            <td>{{$comment['tournament_name']}}</td>
                            <td>{{$comment['buy_in']}}</td>
                            <td>{{$comment['entry_fee']}}</td>
                            <td>{{$comment['created_date']}}</td>
                            @if($comment['visible'] == 0)
                                <td>No</td>

                            @else
                                <td>Yes</td>
                                @endif
                            <td>{{$comment['comment']}}</td>
                            <td>{!! link_to('admin/tournament-comments/delete/'.$comment['id'], 'Delete', array('class' =>'btn btn-primary')) !!}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop