<?php
/**
* @package sportman01
* @version 1.1
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

//pass Itemid thru
global $Itemid;
$Itemid ? $Itemid = '&amp;Itemid='.$Itemid : $Itemid = '';
?>

<div id="bettaWrap" >
  <div class="moduletable">
    <h3>My Account</h3>
    <div class="innerWrap">
      <div class="hdrBar"><div id="hdrBar_account"></div>Account Transactions</div>
      <div class="accountButts">
        <div id="ideposit" class="accountButt"><a href="user/account/instant-deposit">
          <img src="/components/com_payment/images/icon_ideposit.png" width="80px" height="64px" border="0" alt="Instant Deposit"><br />Instant Deposit</a>
        </div>
        <div class="accountButt"><a href="user/account/withdrawal-request">
          <img src="/components/com_payment/images/icon_withdrawl.png" width="80px" height="64px" border="0" alt="Instant Deposit"><br />Withdrawal</a>
        </div>
        <div class="accountButt"><a href="user/account/bpay-deposit">
          <img src="/components/com_payment/images/icon_bpay.png" width="80px" height="64px" border="0" alt="Instant Deposit"><br />BPAY&#174;</a>
        </div>
        <div class="accountButt"><a href="user/account/bank-deposit">
          <img src="/components/com_payment/images/icon_deposit.png" width="80px" height="64px" border="0" alt="Instant Deposit"><br />Deposit</a>
        </div>
        <div class="clr"></div>
      </div>

      <div class="hdrBar"><div id="hdrBar_settings"></div>Account Details</div>
      <div class="accountButts">
        <div class="accountButt"><a href="user/account/settings">
          <img src="/components/com_payment/images/icon_myaccount1.png" width="80px" height="64px" border="0" alt="Account Settings"><br />Account Settings</a>
        </div>
        <div class="accountButt"><a href="user/account/transactions">
          <img src="/components/com_payment/images/icon_mybets.png" width="80px" height="64px" border="0" alt="My Account Transactions"><br />My Account Transactions</a>
        </div>
        <div class="accountButt"><a href="user/account/tournament-transactions">
          <img src="/components/com_payment/images/icon_mybets.png" width="80px" height="64px" border="0" alt="My Tournament Transactions"><br />My Tournament Transactions</a>
        </div>
        <div class="accountButt"><a href="user/exclude" onclick="return confirm('Clicking ok will block you from accessing the site for a period of 1 week, and log you out. Click cancel to stay.');">
          <img src="/components/com_payment/images/icon_myaccount1.png" width="80px" height="64px" border="0" alt="Account Settings"><br />Exclude Yourself</a>
        </div>
        <div class="accountButt"><a href="user/refer-a-friend">
          <img src="/components/com_payment/images/icon_myaccount1.png" width="80px" height="64px" border="0" alt="Refer a Friend"><br />Refer a Friend</a>
        </div>
        <div class="clr"></div>
      </div>

    </div>
  </div>
</div>

