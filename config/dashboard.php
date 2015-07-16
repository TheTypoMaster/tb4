<?php
return array(

    'base_api_endpoint' =>  env('DASHBOARD_ENDPOINT', 'http:/dashboard.dev/api/v1/'),

    'api_user' => env('DASHBOARD_USERNAME', ''),

    'api_password' => env('DASHBOARD_PASSWORD', ''),

    'queue' => env('DASHBOARD_QUEUE', 'dashboard-notification'),

    'error_email_to' => array('address' => env('DASHBOARD_FROM_ADDRESS', 'alerts@topbetta.com'),
								'name' => env('DASHBOARD_FROM_NAME', 'alerts')),

    'error_email_from' => array('address' => env('DASHBOARD_TO_ADDRESS', 'alerts@topbetta.com'),
								'name' => env('DASHBOARD_TO_NAME', 'alerts')),
);