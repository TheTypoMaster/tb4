<?php
return array(

    "class" => "TopBetta\\Services\\Email\\ThirdParty\\Vision6",


    "data"  => array(

        'list_id'  => env("VISION_LIST_ID"),

        "poll_time" => 1000,

        "api_key"   => env("VISION_API_KEY"),

        "fields"    => array(
            "email"              => "Email",
            "first_name"         => "First Name",
            "last_name"          => "Surname",
            "mobile"             => "Mobile Phone",
        ),

    ),

    "connection_timeout" => 10,
);