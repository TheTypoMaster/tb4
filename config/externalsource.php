<?php

return array(
    "queue" => "external-source-notification",

    "notification_services" => array(
        "deposit" => array(
            "puntersclub" => 'TopBetta\Services\ExternalSourceNotifications\Queue\PuntersClub\DepositNotificationService',
        )
    ),

    "message_authentication" => ENV("EXTERNAL_MESSAGE_AUTHENTICATION", true),
);