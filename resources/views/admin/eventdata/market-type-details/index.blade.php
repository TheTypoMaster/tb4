@extends('admin.layouts.master')

@section('main')
<div class="row">
    <div class="col-lg-12">
        <div class="row page-header">
            <h2 class="col-lg-8">Market Type Details
                {!! link_to_route('admin.market-type-details.create', 'Create', array(), array('class' => 'btn btn-info')) !!}
            </h2>
        </div>

        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Id</th>
                <th>Sport</th>
                <th>Market Type</th>
                <th>Max Winning Selections</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($marketTypeDetails as $detail)
                <tr>
                    <td>{{ $detail->id }}</td>
                    <td>{{ $detail->sport->name }}</td>
                    <td>{{ $detail->marketType->name }}</td>
                    <td>{{ $detail->max_winning_selections }}</td>
                    <td>{!! link_to_route('admin.market-type-details.edit', "Edit", array($detail->id), array('class' => 'btn btn-warning')) !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {!! $marketTypeDetails->render(); !!}
    </div>
</div>

@stop