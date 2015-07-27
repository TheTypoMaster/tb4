@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            @include('admin.users.partials.header')

            {!! Form::model($depositLimit, array("route"=>array("admin.users.deposit-limit.update", $user->id, 'get'), "method" => "PUT")) !!}

            <div class="form-group">
                {!! Form::label('amount', 'Limit Amount: ') !!}
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    {!! Form::number('amount', null, array('class'=>'form-control')) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('notes', 'Notes: ') !!}
                {!! Form::textarea('notes', null, array('class' => 'form-control')) !!}
            </div>

            <div class="form-group">
                {!! Form::submit("Save", array('class' => 'form-control btn btn-primary')) !!}
            </div>

        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop