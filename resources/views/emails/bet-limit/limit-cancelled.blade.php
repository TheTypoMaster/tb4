<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>TopBetta User Bet Limit Notification</h2>

<div>
    User {{ $user->username }} ({{ $user->user_id }}) has cancelled the request to increase bet limit <br/>
    from {!! bcdiv($user->bet_limit, 100, 2) !!} <br/>
    to {!! ($user->requested_bet_limit == -1 ? 'no limit' : bcdiv($user->requested_bet_limit, 100, 2)) !!} <br/>
    on {!! date('j/n/Y') !!} <br/>
</div>

</body>
</html>