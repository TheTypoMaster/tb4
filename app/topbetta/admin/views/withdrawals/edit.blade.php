@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Withdrawal Request for {{ $withdrawal->user->username }}</h2>

            {{ Form::model($withdrawal, array("method" => "PUT", "route" => array("admin.withdrawals.update", $withdrawal->id))) }}
            <div class="form-group">
                {{ Form::label('amount', 'Amount') }}
                <div class="input-group">
                    <div class="input-group-addon">$</div>
                    {{ Form::text('amount', number_format($withdrawal->amount/100, 2), array("class" => "form-control", "disabled" => "disabled")) }}
                </div>
            </div>

            <div class="form-group">
                {{ Form::label('withdrawal_type', "Withdrawal Type: ") }}
                {{ Form::text('withdrawal_type', $withdrawal->type->name, array("class" => "form-control", "disabled" => "disabled")) }}
            </div>

            <div class="form-group">
                {{ Form::label('requested_date', "Requested Date: ") }}
                {{ Form::text('requested_date', null, array("class" => "form-control", "disabled" => "disabled")) }}
            </div>

            <div class="form-group">
                {{ Form::label('approved', "Approve: ") }}
                <div class="form-group">
                    <label class="radio-inline">
                        {{ Form::radio('approved_flag', 0, true) }}  No
                    </label>
                    <label class="radio-inline">
                        {{ Form::radio('approved_flag', 1, false) }} Yes
                    </label>
                </div>
            </div>

            <div class="form-group">
                {{ Form::label('email', "Send Email: ") }}
                <div class="form-group">
                    <label class="radio-inline">
                        {{ Form::radio('email_flag', 1, false) }} Yes
                    </label>
                    <label class="radio-inline">
                        {{ Form::radio('email_flag', 0, true) }}  No
                    </label>
                </div>
            </div>

            <div class="form-group">
                {{ Form::label('notes', "Notes: ") }}
                {{ Form::textarea('notes', null, array("class"=>"form-control")) }}
            </div>

            <div class="form-group" style="display:none;" id="transaction-notes">
                {{ Form::label('transaction_notes', "Transaction Notes: ") }}
                {{ Form::textarea('transaction_notes', null, array("class"=>"form-control")) }}
            </div>

            {{ Form::submit("Save", array("class" => "form-control btn btn-primary")) }}
        </div>
    </div>

    <script type="text/javascript">
        $("input[name='approved_flag']").click(function(){
            if($(this).val() == 1) {
                $("#transaction-notes").show();
            } else {
                $("#transaction-notes").hide();
            }
        });
    </script>

@stop