@extends('emails.layouts.standard_email_template')

@section('email-body')
    <p>
        Congratulations {{ $user->username }}! You're a winner in {{ $tournament->name }}.  <br/>

        {{ $body }} <br/>

        Login to check out the details https://www.topbetta.com <br/>

        Thanks for playing Topbetta!<br/>

        Regards,<br/>

        The TopBetta Team <br/>
        help@topbetta.com <br/>
    </p>
@stop