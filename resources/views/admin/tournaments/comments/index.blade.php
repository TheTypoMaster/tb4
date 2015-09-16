@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">

            </div>
            <div class="row pull-right" style="margin-right: 60px; margin-bottom: 20px;">
                {!! Form::open(['url' => 'admin/tournament-comments/store']) !!}
                {!! Form::select('tournament', $tournament_list) !!}
                {!! Form::label('new_comment', 'Add Comment: ') !!}
                {!! Form::input('text', 'new_comment', '') !!}
                {!! Form::submit('Add', ['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
            </div>
            <div class="row">
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Tournament</th>
                        <th>Time of Comment</th>
                        <th>Visible</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                    @foreach($comments as $key => $comment)
                        <tr>
                            <td>{{$comment['id']}}</td>
                            <td>{{$comment['username']}}</td>
                            <td>{{$comment['tournament']}}</td>
                            <td>{{$comment['created_date']}}</td>
                            <td>Visible</td>
                            <td>{{$comment['comment']}}</td>
                            <td>{!! link_to('admin/tournament-comments/delete/'.$comment['id'], 'Delete', array('class' =>'btn btn-primary')) !!}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop