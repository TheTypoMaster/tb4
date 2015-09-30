@extends('admin.layouts.master')

@section('main')

@section('market-type-group')
    @if(isset($model)))
    <div class="form-group">
        {!! Form::label('market_type_group_id', 'Group: ') !!}
        {!! Form::select('market_type_group_id', $data['extraFields']["Market Rules"]["market_type_group_list"], $model->market_type_group_id, array('id' => 'groups', 'class' => 'form-control')) !!}
    </div>
    @else
        <div class="form-group">
            {!! Form::label('market_type_group_id', 'Group: ') !!}
            {!! Form::select('market_type_group_id', $data['extraFields']["Market Rules"]["market_type_group_list"], '', array('id' => 'groups', 'class' => 'form-control')) !!}
        </div>
    @endif
@stop

    @include('admin.eventdata.partials.templates.form-template', $data)
@stop