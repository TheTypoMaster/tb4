<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h3>Welcome {{$user->name}}</h3>

<p>
    To activate your account please <a href="{{$activationUrl}}/{{$user->activation}}">click here</a>
</p>

<p>
    <br>TopBetta<br>
    Phone: 1300 886 503<br>
    Email: help@topbetta.com
</p>
</body>
</html>