<?php

return array (

    "apiEndPoints"   => array (
        "initiateTransaction"   => "https://poliapi.apac.paywithpoli.com/api/Transaction/Initiate",
        "getTransactionDetails" => "https://poliapi.apac.paywithpoli.com/api/Transaction/GetTransaction",
    ),

    "merchantId"        => "S6100795",
    "merchantPassword"  => "sTxdEaUSJlJMOyA",

    "timeOut"           => 900,

    "nudgeUrl"          => "api.v2.poli-deposit.store",

    "redirectRoute"     => "api.v2.poli-deposit.show",

    "homePage"          => env("POLI_HOME_PAGE", "https://topbetta.com.au"),

    "frontendReturnUrl" => env("POLI_RETURN_URL", "http://topbetta.com.au/"),

);