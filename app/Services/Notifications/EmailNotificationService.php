<?php namespace TopBetta\Services\Notifications;

/**
 * Coded by Oliver Shanahan
 * File creation date: 19/01/15
 * File creation time: 14:15
 * Project: tb4
 */

use Mail;
use View;
use Log;

/**
 * Class EmailNotificationService
 * @package TopBetta\Services\Notifications
 */
class EmailNotificationService {

    /**
     * Send email notification
     *
     * @param $toEmailAddress
     * @param $toName
     * @param $fromEmailAddress
     * @param $fromName
     * @param $subject
     * @param $body
     */
    public function notifyByEmail($toEmailAddress, $toName, $fromEmailAddress, $fromName, $subject, $body)
    {

        $emailDetails = array(
            'to_email' => $toEmailAddress,
            'to_name' => $toName,
            'from_email' => $fromEmailAddress,
            'from_name' => $fromName,
            'subject' => $subject
        );

        $data = array(
            'detail'=> $body

        );

        Mail::send('topbetta::emails.alert_notification', $data, function($message) use ($emailDetails)
        {
            $message->from($emailDetails['from_email'], $emailDetails['from_name']);
            $message->to($emailDetails['to_email'], $emailDetails['to_email'])
                    ->subject($emailDetails['subject']);
        });

    }

}