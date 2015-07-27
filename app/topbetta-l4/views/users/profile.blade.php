@extends('layouts.default')
<div class="container">
    <div>
        @if(Auth::check())
            <p>Welcome to your profile page {{Auth::user()->username}}</p>
        @endif
    </div>
</div>