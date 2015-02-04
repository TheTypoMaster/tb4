<?php

return array(

    /*
     |--------------------------------------------------------------------------
     | Laravel CORS Defaults
     |--------------------------------------------------------------------------
     |
     | The defaults are the default values applied to all the paths that match,
     | unless overridden in a specific URL configuration.
     | If you want them to apply to everything, you must define a path with *.
     |
     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*') 
     | to accept any value, the allowed methods however have to be explicitly listed.
     |
     */
    'defaults' => array(
        'supportsCredentials' => false,
        'allowedOrigins' => array("http://localhost:9778"),
        'allowedHeaders' => array(),
        'allowedMethods' => array(),
        'exposedHeaders' => array(),
        'maxAge' => 0,
        'hosts' => array(),
    ),

    'paths' => array(
        '^/' => array(
            'supportsCredentials' => true,
            'allowedOrigins' => array(
                "http://localhost:9778",
                "http://beta.mugbookie.com",
                "http://localhost",
                "http://beta.tb4.dev",
                "http://tb4test.mugbookie.com",
                "http://192.168.0.31:9778",
                "https://www.topbetta.com.au",
                "http://jason.mugbookie.com",
                "http://jasontb.mugbookie.com",
                "http://evan.mugbookie.com",
                "http://mic.mugbookie.com",
                "http://greg.mugbookie.com"
            ),
            'allowedHeaders' => array('*'),
            'allowedMethods' => array('POST', 'PUT', 'GET', 'DELETE'),
            'maxAge' => 3600,
        ),
    ),

);
