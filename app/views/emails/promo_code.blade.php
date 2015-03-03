<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h3>TopBetta Promo Code {{$promoCode}} entered for user {{$user->username}}</h3>

<h5>Details</h5>

<dl>
    <dt>Promo Code: </dt>
    <dd>{{ $promoCode }}</dd>

    <dt>User Id: </dt>
    <dd>{{ $user->id }}</dd>

    <dt>User Name: </dt>
    <dd>{{ $user->username  }}</dd>

    <dt>Name: </dt>
    <dd>{{ $user->name  }}</dd>

    <dt>Amount: </dt>
    <dd>{{ $amount }}</dd>

    <dt>Payment Method: </dt>
    <dd>{{ $paymentMethod }}</dd>
</dl>

<p>
    <br>TopBetta<br>
    Phone: 1300 886 503<br>
    Email: help@topbetta.com
</p>
</body>
</html>