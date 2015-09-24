@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Market Type Groups
                    <a href="{{URL::to('admin/market-groups/create')}}"><button class="btn btn-primary">Create</button></a>
                </h2>
            </div>

            <div class="row">
                <table class="table" style="margin-left: 20px; margin-right: 20px;">
                    <tr>
                        <th>Group ID</th>
                        <th>Group Name</th>
                        <th>Description</th>
                        <th>Icon</th>
                        <th colspan="2" align="centre">Action</th>
                    </tr>

                @foreach($marketTypeGroups as $marketTypeGroup)
                    <tr>
                        <td>{{$marketTypeGroup->market_type_group_id}}</td>
                        <td>{{$marketTypeGroup->market_type_group_name}}</td>
                        <td>{{$marketTypeGroup->market_type_group_description}}</td>
                        <td>{{$marketTypeGroup->icon_id}}</td>
                        <td><a href="{{URL::to('admin/market-groups/edit/'.$marketTypeGroup->market_type_group_id)}}"><button class="btn btn-primary">Edit</button></a></td>
                        <td><a href="{{URL::to('admin/market-groups/delete/'.$marketTypeGroup->market_type_group_id)}}"><button class="btn btn-primary">Delete</button></a></td>

                    </tr>
                    @endforeach
                </table>


                {{-- add pagination --}}
                {{ $marketTypeGroups->render() }}
            </div>

        </div>
    </div>

@stop