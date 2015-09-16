<?php

return array(
    'riskManagerAPI' => env('RISK_ENDPOINT', 'http://risk.dev/api/v1'),
    'productionHost' => env('RISK_HOST', 'testing'),
    'RISK_FEED_API' => env('RISK_FEED_API', 'http://risk.dev/api/backend/v1'),
    'RISK_RACE_DATA_URI' => env('RISK_FEED_API_URI', '/racing-feed'),
    'RISK_RACE_RESULT_DATA_URI' => env('RISK_RESULT_FEED_API_URI', '/results-feed'),
);