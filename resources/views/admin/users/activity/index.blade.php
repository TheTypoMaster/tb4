@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            @if( $user->topbettauser )
                @include('admin.users.partials.header')
            @endif

            <div class="row" style="margin-left: 20px; margin-right: 20px;">
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Details</th>
                        <th>Action Time</th>
                    </tr>

                    @foreach($activity_list as $key => $activity)
                        <tr>
                            <td>{{ $activity->id }}</td>
                            <td>{{ $activity->action }}</td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->details }}</td>
                            <td>{{ $activity->updated_at }}</td>
                        </tr>
                    @endforeach
                </table>

                {!! $activity_list->render() !!}
            </div>
        </div>
    </div>
@stop