<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/02/2015
 * Time: 11:28 AM
 */

namespace TopBetta\Services\Accounting;

use Mail;
use TopBetta\Repositories\Contracts\ConfigurationRepositoryInterface;
use TopBetta\Repositories\Contracts\WithdrawalRequestRepositoryInterface;

class WithdrawalService {

    const WITHDRAWAL_EMAIL_CONFIG = 'withdrawal_email';

    const WITHDRAWAL_EMAIL_VARIABLE_CONFIG = 'withdrawal_email_variables';

    /**
     * @var WithdrawalRequestRepositoryInterface
     */
    private $withdrawalRequestRepository;
    /**
     * @var ConfigurationRepositoryInterface
     */
    private $configurationRepository;

    public function __construct(WithdrawalRequestRepositoryInterface $withdrawalRequestRepository, ConfigurationRepositoryInterface $configurationRepository)
    {
        $this->withdrawalRequestRepository = $withdrawalRequestRepository;
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * Gets the sum of the total approved withdrawals for a user
     * @param $userId
     * @return mixed
     */
    public function getTotalApprovedWithdrawalsForUser($userId)
    {
        return $this->withdrawalRequestRepository->getTotalWithdrawalsForUserWithApproved($userId, true);
    }

    public function sendApprovalEmail($withdrawalId)
    {
        return $this->sendWithdrawalProcessedEmail($withdrawalId, 'withdrawal_approval_email');
    }

    public function sendDenialEmail($withdrawalId)
    {
        return $this->sendWithdrawalProcessedEmail($withdrawalId, 'withdrawal_denial_email');
    }

    public function sendWithdrawalProcessedEmail($withdrawalId, $withdrawalName)
    {
        //get the withdrawal and config
        $withdrawal = $this->withdrawalRequestRepository->findWithUserAndType($withdrawalId);
        $emails = $this->configurationRepository->getConfigByName(self::WITHDRAWAL_EMAIL_CONFIG);
        $variables = $this->configurationRepository->getConfigByName(self::WITHDRAWAL_EMAIL_VARIABLE_CONFIG, true);

        $emailBody = $emails->{$withdrawalName.'_body'};

        //replace variables in email body
        foreach($variables as $key=>$variable) {
            if($value = array_get($variable, 'value', null)) {
                if($key == 'amount') {
                    $emailBody = str_replace('['.$key.']', '$'.number_format(object_get($withdrawal, $value, 0)/100, 2), $emailBody);
                } else if ($key == 'amount raw') {
                    $emailBody = str_replace('[' . $key . ']', object_get($withdrawal, $value, 0) / 100, $emailBody);
                } else {
                    $emailBody = str_replace('[' . $key . ']', object_get($withdrawal, $value, 0), $emailBody);
                }
            }
        }



        $emailBody = str_replace('[help email]', $emails->help_email, $emailBody);

        //send the email
        Mail::send('emails.withdrawals.processed', array("body" => $emailBody), function($message) use ($withdrawal, $emails, $withdrawalName) {
            $message->to($withdrawal->user->email, $withdrawal->user->name)
                ->subject($emails->{$withdrawalName.'_subject'})
                ->from($emails->sender_email, $emails->sender_name);
        });

        return true;
    }

}