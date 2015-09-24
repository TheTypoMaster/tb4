@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Tournament Event Groups
                    {{--<a href="{{URL::to('admin/event-groups/create')}}"><button class="btn btn-primary">Create</button></a>--}}
                </h2>
            </div>

            <div class="row">
                <table class="table" style="margin-left: 20px; margin-right: 20px;">
                    <tr>
                        <th>Event Group ID</th>
                        <th>Event Group Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th colspan="2" align="centre">Action</th>
                    </tr>


                </table>

                {{-- add pagination --}}

            </div>

        </div>
    </div>

@stop