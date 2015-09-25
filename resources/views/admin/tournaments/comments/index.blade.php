@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">

            </div>

            <div class="row">
                <div class="pull-right" style="margin-right: 60px; margin-bottom: 20px;">
                    {!! Form::label('tournament_id', 'Tournament ID.: ') !!}
                    {!! Form::input('text', 'tournament_id', '') !!}
                    {!! Form::label('username', 'Username: ', '') !!}
                    {!! Form::input('text', 'username', '') !!}
                    {!! Form::label('visible', 'Visible Only: ') !!}
                    {!! Form::checkbox('visible', 0, false) !!}
                    {!! Form::input('button', 'submit', 'Filter', ['class' => 'btn btn-primary', 'id' => 'submit']) !!}
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
            <div class="row" style="margin-right: 20px;">
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
                        {{--<th>Delete</th>--}}
                        <th>Block</th>
                    </tr>
                    @foreach($comments as $key => $comment)
                        <tr class="list">
                            <td class="comment_id">{{$comment['id']}}</td>
                            <td class="username">{{$comment['username']}}</td>
                            <td class="tournament_id">{{$comment['tournament_id']}}</td>
                            <td class="tournament_name">{{$comment['tournament_name']}}</td>
                            <td class="buy_in">{{$comment['buy_in']}}</td>
                            <td class="entry_fee">{{$comment['entry_fee']}}</td>
                            <td class="created_date">{{$comment['created_date']}}</td>
                            @if($comment['visible'] == 0)
                                <td class="visible">No</td>

                            @else
                                <td class="visible">Yes</td>
                            @endif
                            <td>{{$comment['comment']}}</td>
                            {{--<td>{!! link_to('admin/tournament-comments/delete/'.$comment['id'], 'Delete', array('class' =>'btn btn-primary')) !!}</td>--}}
                            <td>{!! link_to('admin/tournament-comments/block/'.$comment['id'], 'Change', array('class' =>'btn btn-primary')) !!}</td>
                        </tr>
                    @endforeach
                </table>

                {{--pagination--}}
                @if($pagination['total_pages'] >1)
                    <nav>
                        <ul class="pagination">
                            <li>
                                <a href="{{URL::to($pagination['previous_page_url'])}}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            @for($i=1; $i<=$pagination['total_pages']; $i++)
                                @if($pagination['current_page'] == $i)

                                    <li class="active">{!! link_to('admin/tournament-comments?page=' . $i, $i, array()) !!}</li>
                                @else
                                    <li>{!! link_to('admin/tournament-comments?page=' . $i, $i, array()) !!}</li>
                                @endif
                            @endfor

                            @if($pagination['has_more_pages'])
                                <li>
                                    <a href="{{URL::to($pagination['next_page_url'])}}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#submit').click(function () {
                var tournament_id = $('#tournament_id').val();
                var username = $('#username').val();
                var visible_only = $('#visible').is(':checked');
                $('.list').show();
                if (username) {
                    $('.list').filter(function (index) {
                        return $(this).find('.username').text().toLowerCase().indexOf(username.toLowerCase()) < 0;
//                        return $(this).find('.username').text() != username;
                    }).hide();
                }

                if (tournament_id) {
                    $('.list').filter(function (index) {
                        return $(this).find('.tournament_id').text() != tournament_id;
                    }).hide();
                }

                if (visible_only) {
                    $('.list').filter(function (index) {
                        return $(this).find('.visible').text() != 'Yes';
                    }).hide();
                }
            });
        });
    </script>
@stop

