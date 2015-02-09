<?php

return array (

    "apiEndPoints"   => array (
        "initiateTransaction"   => "https://poliapi.apac.paywithpoli.com/api/Transaction/Initiate",
        "getTransactionDetails" => "https://poliapi.apac.paywithpoli.com/api/Transaction/GetTransaction",
    ),

    "merchantId"        => "S6100795",
    "merchantPassword"  => "sTxdEaUSJlJMOyA",

    "timeOut"           => 900,

    "nudgeUrl"          => "api.v1.users.poli-deposit.store",

    "redirectRoute"     => "api.v1.users.poli-deposit.show",

    "homePage"          => "https://topbetta.com.au",

    "frontendReturnUrl" => "http://topbetta.com.au/#poli/",

);