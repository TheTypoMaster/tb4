<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/08/2015
 * Time: 4:46 PM
 */

namespace TopBetta\Services\Tournaments\Email;


use TopBetta\Services\Tournaments\Resulting\TournamentResult;

class TournamentEmailService {

    public function sendWinnerNotification(TournamentResult $result)
    {
        $email = new WinnerNotificationEmail;

        $email->setResult($result);

        \Mail::send('emails.tournaments.winner_notification', array('body' => $email->getBody(), 'user' => $result->getTicket()->user, 'tournament' => $result->getTicket()->tournament), function($message) use ($result) {
            $message->to($result->getTicket()->user->email, $result->getTicket()->user->name)
                ->subject('TopBetta Winner Notification')
                ->from(\Config::get('mail.from.address', 'mail.from.name'));
        });
    }
}