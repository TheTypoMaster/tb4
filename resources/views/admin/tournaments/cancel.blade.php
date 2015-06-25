@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-8">
                    Cancel Tournament -
                    <small>{{ $tournament->eventGroup->name }}({{ number_format($tournament->buy_in/100, 2)}} + {{ number_format($tournament->entry_fee/100, 2) }})</small>
                </h2>
            </div>
        </div>
    </div>

    @if($tournament->tickets->count())
        <div class="row" style="display:block;padding-bottom:75px;">
            <div class="col-lg-12">
                <div class="alert alert-warning" role="alert" >
                    <strong>Warning:</strong> Tournament has {{ $tournament->tickets->count() }} entrants. Cancelling will refund these tickets.
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            {!! Form::open(array('url' => "/admin/tournaments/cancel/" . $tournament->id, "method" => "POST", "class" => "form", 'id' => "cancel-form")) !!}
            <div class="form-group">
                {!! Form::label('reason', 'Reason ') !!}
                {!! Form::textarea('reason', null, array("class" => "form-control")) !!}
            </div>

            <div class="form-group">
                {!! Form::submit('Cancel Tournament', array('class' => 'form-control btn btn-primary')) !!}
            </div>
            {!! Form::close() !!}

        </div>
    </div>

    <script type="text/javascript">
        $('#cancel-form').submit(function(){
            var conf = confirm("Are you sure you want to cancel this tournament?");

            return conf;
        })
    </script>

@stop