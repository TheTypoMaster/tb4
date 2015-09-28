@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Prize Format
                </h2>
            </div>

            <div class="row">
                <table class="table" style="margin-left: 20px; margin-right: 20px;">
                    <tr>
                        <th>ID</th>
                        <th>Keyword</th>
                        <th>Name</th>
                        <th>Short Name</th>
                        <th>Icon</th>
                        <th>Action</th>
                    </tr>

                @foreach($prize_formats as $prize)
                    <tr>
                        <td>{{ $prize->id }}</td>
                        <td>{{ $prize->keyword }}</td>
                        <td>{{ $prize->name }}</td>
                        <td>{{ $prize->short_name }}</td>
                        <td>{{ $prize->icon }}</td>
                        <td><a href="{{URL::to('admin/prize-format/edit/'.$prize->id)}}"><button class="btn btn-primary">Edit</button></a></td>
                    </tr>
                    @endforeach
                </table>

                {{-- add pagination --}}

            </div>

        </div>
    </div>

@stop