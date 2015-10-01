<div class='col-lg-6'>
    {!! Form::model($user, array('method' => 'PUT', 'route' => array('admin.users.update', $user->id))) !!}
    <div class="form-group">
        {!! Form::label('name', 'Name:') !!}
        {!! Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Name')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('username', 'Username:') !!}
        {!! Form::text('username', null, array('class' => 'form-control', 'placeholder' => 'Username')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('email', 'Email:') !!}
        {!! Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email')) !!}
    </div>
</div>

<div class='col-lg-6'>
    <div class="form-group">
        {!! Form::label('first-name', 'First Name:') !!}
        {!! Form::text('first-name', $user->topbettaUser->first_name, array('class' => 'form-control', 'placeholder' => 'First Name')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('last-name', 'Last Name:') !!}
        {!! Form::text('last-name', $user->topbettaUser->last_name, array('class' => 'form-control', 'placeholder' => 'Last Name')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('mobile', 'Mobile:') !!}
        {!! Form::text('mobile', $user->topbettaUser->msisdn, array('class' => 'form-control', 'placeholder' => 'Mobile')) !!}
    </div>
</div>

<div class="col-lg-6">
    <div class="form-group">
        {!! Form::label("identity_verified", 'Check this once the identity of a user has been verified') !!}
        {!! Form::checkbox('identity_verified', 1, '') !!}
    </div>

    <div class="form-group">
        {!! Form::label('doc_type', 'Primary Doc Type') !!}
        {!! Form::select('doc_type', [0 => 'Birth Certificate', 1 => 'Citizenship Certificate', 2 => 'Passport', 3 => 'Driver\'s License', 4 => 'Veda'], $topbetta_user_record->identity_doc, array('class' => 'form-control', 'placeholder' => 'select')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('doc_id', 'Primary Doc ID') !!}
        {!! Form::text('doc_id', $topbetta_user_record->identity_doc_id, array('class' => 'form-control', 'placeholder' => 'Primary Doc ID')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('bsb_number', 'BSB Number') !!}
        {!! Form::text('bsb_number', $topbetta_user_record->bsb_number, array('class' => 'form-control', 'placeholder' => 'BSB Number')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('bank_account_number', 'Bank Account Number') !!}
        {!! Form::text('bank_account_number', $topbetta_user_record->bank_account_number, array('class' => 'form-control', 'placeholder' => 'Bank Account Number')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('bank_account_name', 'Account Name') !!}
        {!! Form::text('bank_account_name', $topbetta_user_record->account_name, array('class' => 'form-control', 'placeholder' => 'Account Name')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Bank_name', 'Bank Name') !!}
        {!! Form::text('Bank_name', $topbetta_user_record->bank_name, array('class' => 'form-control', 'placeholder' => 'Bank Name')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('source', 'Source') !!}
        {!! Form::text('source', $topbetta_user_record->source, array('class' => 'form-control', 'placeholder' => 'Source')) !!}
    </div>

    <?php
    $exclusion_date = \Carbon\Carbon::parse($topbetta_user_record->self_exclusion_date);
    ?>
    <div class="form-group">
        {!! Form::label('exclusion_date', 'Exclusion Date') !!}
        {!! Form::input('date', 'exclusion_date', $exclusion_date->format('Y-m-d'), array('class' => 'form-control', 'placeholder' => 'Mobile')) !!}
    </div>

</div>

<div class="col-lg-6">
    <div class="form-group">
        {!! Form::label('street', 'Street Address') !!}
        {!! Form::text('street', $topbetta_user_record->street, array('class' => 'form-control', 'placeholder' => 'Source')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('suburb', 'Suburb / City') !!}
        {!! Form::text('suburb', $topbetta_user_record->city, array('class' => 'form-control', 'placeholder' => 'Source')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('state', 'State') !!}
        {!! Form::select('state', ['nsw' => 'New South Wales', 'vic' => 'Victoria', 'qld' => 'Queensland', 'sa' => 'South Australia', 'wa' => 'Western Australia', 'nt' => 'Northern Territory',
        'act' => 'Australian Capital Territory', 'tas' => 'Tasmania', 'other' => 'Not in Australia'], $topbetta_user_record->state, array('class' => 'form-control', 'placeholder' => 'Select')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('country', 'Country') !!}
        {!! Form::select('country', $country_list, array('class' => 'form-control', $topbetta_user_record->country, 'placeholder' => 'Source')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('postcode', 'Postcode') !!}
        {!! Form::text('postcode', $topbetta_user_record->postcode, array('class' => 'form-control', 'placeholder' => 'Source')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('heard_about_us', 'Heard About Us') !!}
        {!! Form::select('heard_about_us', ['Australia\'s Top Punter' => 'Australia\'s Top Punter', 'Friend' => 'Friend', 'Word of Mouth' => 'Word of Mouth', 'Advertisement' => 'Advertisement', 'TV Advertisement' => 'TV Advertisement', 'Radio Advertisement' => 'Radio Advertisement', 'Internet' => 'Internet',
        'Promotion' => 'Promotion', 'Other' => 'Other'], $topbetta_user_record->heard_about, array('class' => 'form-control', 'placeholder' => 'Select')) !!}
    </div>
</div>

<div class="col-lg-12">
    <div class="form-group">
        {!! Form::submit('Update', array('class' => 'btn btn-info')) !!}
    </div>
    {!! Form::close() !!}
</div>
@if ($errors->any())
    <ul>
        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
    </ul>
@endif