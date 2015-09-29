@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2>Edit Comment</h2>
            </div>
            <div class="row" style="margin-left: 20px; margin-right: 20px;">
                {!! Form::open(['url'=>'admin/tournament-comments/update/'.$comment->id]) !!}
                {!! Form::label('comment', 'Edit Comment ') !!}
                {!! Form::input('text', 'comment', $comment->comment) !!}
                {!! Form::submit('Update', array('class' => 'btn btn-primary')) !!}
                {!! Form::close() !!}
            </div>

        </div>
    </div>


@stop

