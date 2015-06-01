<?php

return array(

    "prefix" => "admin",

    "resources" => array(
        array("name" => "dashboard", "only" => array("view")),
        array("name" => "users"),
        array("name" => "users.bet-limits"),
        array("name" => "users.bets", "only" => array("view")),
        array("name" => "users.tournaments"),
        array("name" => "users.account-transactions"),
        array("name" => "users.free-credit-transactions"),
        array("name" => "users.withdrawals"),
        array("name" => "bet-limits"),
        array("name" => "bets"),
        array("name" => "withdrawals"),
        array("name" => "account-transactions"),
        array("name" => "free-credit-transactions"),
        array("name" => "tournaments"),
        array("name" => "reports", "only" => array("view")),
        array("name" => "settings"),
        array("name" => "sports"),
        array("name" => "competitions"),
        array("name" => "markets"),
        array("name" => "markettypes"),
        array("name" => "events"),
        array("name" => "selections"),
        array("name" => "selectionprices"),
        array("name" => "users.deposit-limit"),
        array("name" => "promotions"),
        array("name" => "free-credit-management"),
    )
);