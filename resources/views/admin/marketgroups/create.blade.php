@extends('admin.layouts.master')

@section('main')

    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">Market Type Group
                    {{--<a href="{{URL::to('admin/market-groups/create')}}"><button class="btn btn-primary">Create</button></a>--}}
                </h2>
            </div>

            <div class="row" style="margin-left: 20px; margin-right: 20px;">
            {!! Form::open(['url' => 'admin/market-groups/store']) !!}

                <div class="form-group">
                    {!! Form::label('market_type_group_name', 'Group Name', array()) !!}<br/>
                    {!! Form::Input('text', 'market_type_group_name', '', array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('market_type_group_description', 'Group Description', array()) !!}<br/>
                    {!! Form::Input('text', 'market_type_group_description', '', array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('icon_id', 'Icon ID', array()) !!}<br/>
                    {!! Form::Input('text', 'icon_id', '', array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    <div class="col-md-6">
                        {!! Form::submit('Save', array('class' => 'btn btn-primary form-control')) !!}
                    </div>
                    <div class="col-md-6">
                        {!! link_to_route('admin.tournaments.index', "Cancel", array(), array('class'=>'btn btn-danger form-control')) !!}
                    </div>
                </div>

            {!! Form::close() !!}

                @if (count($errors) > 0)
                    <div class="alert alert-info alert-dismissable col-lg-11">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- add pagination --}}

            </div>

        </div>
    </div>

@stop