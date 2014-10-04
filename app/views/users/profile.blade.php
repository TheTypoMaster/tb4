@extends('layouts.default')
<div class="container">
    <div>
        @if(Auth::check())
            <p>Welcome to your profile page {{Auth::user()->first_name}} - {{Auth::user()->last_name}}</p>
        @endif
    </div>
</div>