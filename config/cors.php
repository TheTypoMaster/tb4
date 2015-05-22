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
        'supportsCredentials' => true,
        'allowedOrigins' => array(),
        'allowedHeaders' => array(),
        'allowedMethods' => array(),
        'exposedHeaders' => array(),
        'maxAge' => 0,
        'hosts' => array(),
    ),

    'paths' => array(
        '^/' => array(
            'allowedOrigins' => array(
				"https://www.topbetta.com.au", // Production Website
                "https://services.topbetta.com.au", // Production API
                "http://tb4test.mugbookie.com", // Test website
                "http://testing1.mugbookie.com", // Test API
                "http://beta.mugbookie.com",
                "http://jason.mugbookie.com",
                "http://jasontb.mugbookie.com",
                "http://evan.mugbookie.com",
                "http://mic.mugbookie.com",
                "http://greg.mugbookie.com",
                "http://topbetta.dev", // Development Website
                "http://services.dev" // Development API"
                ),
            'allowedHeaders' => array('*'),
			'allowedMethods' => array('POST', 'PUT', 'GET', 'DELETE'),
            'maxAge' => 3600,
        ),
//        '*' => array(
//            'allowedOrigins' => array('*'),
//            'allowedHeaders' => array('*'),
//
//            'maxAge' => 3600,
//           // 'hosts' => array('api.*'),
//        ),
    ),

);
