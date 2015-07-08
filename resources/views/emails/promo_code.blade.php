<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h3>TopBetta Promo Code {{$promoCode}} entered for user {{$user->username}}</h3>

<h5>Details</h5>

<ul>
    <li><b>Promo Code:</b> {{ $promoCode }}</li>

    <li><b>User Id:</b> {{ $user->id }}</li>

    <li><b>User Name:</b> {{ $user->username  }}</li>

    <li><b>Name:</b> {{ $user->name  }}</li>

    <li><b>Amount:</b> {{ number_format($amount / 100, 2) }}</li>

    <li><b>Payment Method:</b> {{ $paymentMethod }}</li>
</ul>

<p>
    <br>TopBetta<br>
    Phone: 1300 886 503<br>
    Email: help@topbetta.com
</p>
</body>
</html>