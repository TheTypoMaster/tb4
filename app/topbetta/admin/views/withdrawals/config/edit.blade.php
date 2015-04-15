@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Withdrawal Config</h2>
        </div>

        <a href='#variables' aria-expanded="false" aria-controls="variables" data-toggle="collapse">Variables</a>
        <div class="collapse" id="variables">
            <dl>
                @foreach($variables as $name=>$variable)
                    <dt>[{{ $name }}] : </dt>
                    <dd>{{ $variable['description'] }}</dd>
                @endforeach
            </dl>
        </div>

        {{ Form::model($config, array("route" => array("admin.withdrawal-config.update"), "method" => "PUT")) }}

        <div class="form-group">
            {{ Form::label('help_email', "Help Email: ") }}
            {{ Form::text('help_email', null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::label('sender_email', "Sender's Email: ") }}
            {{ Form::text('sender_email', null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::label('sender_name', "Sender's  Name: ") }}
            {{ Form::text('sender_name', null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::label('withdrawal_notify_email_subject', "Withdrawal Notifying Email Subject: ") }}
            {{ Form::text('withdrawal_notify_email_subject', null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::label('withdrawal_notify_email_body', "Withdrawal Notifying Email Body: ") }}
            {{ Form::textarea('withdrawal_notify_email_body', null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::label('withdrawal_approval_email_subject', "Withdrawal Approval Email Subject: ") }}
            {{ Form::text('withdrawal_approval_email_subject', null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::label('withdrawal_approval_email_body', "Withdrawal Approval Email Body: ") }}
            {{ Form::textarea('withdrawal_approval_email_body', null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::label('withdrawal_denial_email_subject', "Withdrawal Denial Email Subject: ") }}
            {{ Form::text('withdrawal_denial_email_subject', null, array("class" => "form-control")) }}
        </div>

        <div class="form-group">
            {{ Form::label('withdrawal_denial_email_body', "Withdrawal Denial Email Body: ") }}
            {{ Form::textarea('withdrawal_denial_email_body', null, array("class" => "form-control")) }}
        </div>

        {{ Form::submit('Save', array("class" => "btn btn-primary")) }}

        {{ Form::close() }}
    </div>

@stop