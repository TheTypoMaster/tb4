@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Create Market

            </h2>
            <ul class="nav nav-tabs">
                <span class='pull-right'>{!! link_to_route('admin.events.index', 'Back to Events', array("q" => $search), array('class' => 'btn btn-outline btn-warning')) !!}</span>
            </ul>
            <div class='col-lg-6'>
                {!! Form::open(array('method' => 'POST', 'route' => array('admin.markets.store', "event_id" => $event->id, "q" => $search))) !!}
                <div class="form-group">
                    {!! Form::label('event', 'Event') !!}
                    {!! Form::text('event', $event->name, array('class' => 'form-control', 'disabled')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('market_type_id', 'Market Type:') !!}
                    {!! Form::select('market_type_id', $marketTypes->lists('name', 'id')->all(), null, array('class' => 'form-control select2')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('display_flag', 'Betting Open on Topbetta:') !!}
                    {!! Form::select('display_flag', array(
                                                        '1' => 'Yes',
                                                        '0' => 'No'),null, array('class' => 'form-control selected')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('market_status', 'Market Status:') !!}
                    {!! Form::select('market_status', array(
                                        '' => 'No Status (Racing)',
                                        'O' => 'Open',
                                        'C' => 'Closed',
                                        'R' => 'Resulted',
                                        'D' => 'Deleted (Not shown in sports)'), null, array('class' => 'form-control')) !!}
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    {!! Form::submit('Create', array('class' => 'btn btn-info')) !!}
                </div>
                {!! Form::close() !!}
            </div>
            @if ($errors->any())
                <ul>
                    {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                </ul>
            @endif


        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop