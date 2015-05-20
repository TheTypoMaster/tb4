{!! Form::open(array("method" => "POST", "route" => array($storeRoute, $user->id))) !!}

<div class="form-group">
    <label for="tranasction_type">Tranasction Type</label>
    <select class="form-control" name="transaction_type">
        @foreach($transactionTypes as $transactionType)
            <option value="{{$transactionType->keyword}}">{{ $transactionType->name }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    {!! Form::label('amount', 'Amount') !!}
    <div class="input-group">
        <div class="input-group-addon">$</div>
        {!! Form::text('amount', null, array("class" => "form-control")) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('notes', 'Notes') !!}
    {!! Form::text('notes', null, array("class" => "form-control")) !!}
</div>

{!! Form::submit("Save", array("class" => "form-control btn btn-primary")) !!}

{!! Form::close() !!}