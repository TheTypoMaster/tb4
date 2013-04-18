<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * Referral Plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */

class plgUserReferral extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgUserReferral(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

  /**
   *  The plugin function to set up referral information,
   *  pay referrer tournament dollars and send admin notifcations
   *
   * @param array user attributes
   * @param boolean true if the user is new
   * @param boolean true if the user was successfully stored
   * @param string error message
   * @return void
   */
  function onAfterStoreUser($user, $isnew, $success, $msg )
  {
    global $mainframe;
    if($mainframe->isAdmin() )
    {
    	return;
    }

    include_once( JPATH_BASE . DS .'components' . DS . "com_user_referral" . DS . "models" . DS . "userreferral.php" );
    $model = new UserReferralModelUserReferral();

    //deal with referral info
    if( $isnew && $success )
    {
      //get referrer id
      $referrerId = JRequest::getVar('ref_id', 'POST');
      //get referrer user
      include_once( JPATH_BASE . DS .'components' . DS . "com_topbetta_user" . DS . "models" . DS . "topbettauser.php" );
      $userModel	= new TopbettaUserModelTopbettaUser();
      $referrerUser	= $userModel->getUser((int)$referrerId);

      //only proceed if the referrer_id exists
      if( !is_null($referrerUser) )
      {
        $params = array(
          'referrer_id' => $referrerId,
          'friend_id' => $user['id'],
          'tournament_transaction_id' => null,
          'paid_flag' => 0
        );

        $model->store( $params );
      }
    }
    else if( $success )
    {
      //get referral record
      $referral = $model->getReferralByFriendId( $user['id'] );

      //if the user is not activated and not paid, pay referrer tournament dollars
      if( $referral && 0 == $user->block && 0 == $referral->paid_flag )
      {
        //init tournament dollars model
        include_once( JPATH_BASE . DS . 'components' . DS . "com_tournamentdollars" . DS . "models" . DS . "tournamenttransaction.php" );
        $tournModel = new TournamentdollarsModelTournamenttransaction();
        //get referrer user
        $referrerId = $referral->referrer_id;
        $referrerUser = JFactory::getUser((int)$referrerId);
        //get referral payment amount
        $config =& JComponentHelper::getParams( 'com_topbetta_user' );
        $referralPayment = $config->get('referral_payment');
        //get referral payout threshold
        $referralPayoutThreshold = $config->get('referral_payout_threshold');
        //get old and new total referral amount, which are used to calculate if the referral payment reaches threshold
        $oldTotalReferralAmount = $tournModel->getTotal($referrerId, 'referral');
        $newTotalReferralAmount = $oldTotalReferralAmount + $referralPayment;

        //pay referral tourn dollars
        $tournModel->setUserId( $referrerId );

        if( $tournament_transaction_id = $tournModel->increment( $referralPayment, 'referral', 'Referral Payment for ' . $user['username'] ))
        {
          $params = array(
            'id' => $referral->id,
            'referrer_id' => $referrerId,
            'friend_id' => $referral->friend_id,
            'tournament_transaction_id' => $tournament_transaction_id,
            'paid_flag' => 1,
          );
          $model->store( $params );

          //send amdin notification when the payout reaches threshold
          if( floor($newTotalReferralAmount / $referralPayoutThreshold ) >= floor($oldTotalReferralAmount / $referralPayoutThreshold))
          {
            $referralTransactions = $tournModel->listTransactions( $referral->referrer_id, 'referral');
            $this->_sendReferralPaymentNotification($referrerUser, $referralTransactions);
          }
        }
      }
    }
  }

  /**
   * Send admin the notification of referral payments
   *
   * @param object referrer user
   * @param object referral payment transactions
   * @return bool
   */
  private function _sendReferralPaymentNotification( $referrerUser, $referralTransactions )
  {
    $db =& JFactory::getDBO();
    //get sender's email and name
    $config =& JComponentHelper::getParams( 'com_payment' );
    $senderEmail = $config->get('sender_email');
    $senderName = $config->get('sender_name');

    $referralList = '';
    foreach( $referralTransactions as $referralTransaction )
    {
      $referralList .= str_repeat('-', 20 );
      $referralList .= "\nAmount: $" . number_format($referralTransaction->amount / 100, 2, '.', ',');
      $referralList .= "\nDate: " . $referralTransaction->created_date;
      $referralList .= "\nDescription: " . $referralTransaction->notes . "\n";
    }

    $message = "Referral Payment History For " . $referrerUser->username . "\n\n" . $referralList;

    $mailer = new JMAIL();

    $mailer->setSender(array($senderEmail, $senderName));
    $mailer->addReplyTo(array($senderEmail));

    $config		=& JComponentHelper::getParams('com_payment');
    $help_email	= $config->get('help_email');
    
    $mailer->addRecipient(array($help_email, 'Topbetta'));
    $mailer->setSubject( "Referral Payment History");
    $mailer->setBody($message);
    $mailer->IsHTML(false);
    $mailer->Send();
  }
}
?>
