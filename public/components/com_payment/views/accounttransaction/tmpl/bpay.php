<?php
  defined('_JEXEC') or die('Restricted access');

  function mod10($seedval) {
    for ($x=0; $x<strlen($seedval); $x++) {
      $digit = substr($seedval, $x, 1);
      if (strlen($seedval) % 2 == 1) {
        //to multiply by 2 and then by 1
        if ($x/2 == floor($x/2)) $digit *= 2;
        // end multiplicaton
      } else {
        //to multiple first by 1 and then by 2
        if ($x/2 == floor($x/2)) {
          $digit *= 1;
        } else {
          $digit *= 2;
        } //end multiplication
      }
      if (strlen($digit) == 2) $digit = substr($digit, 0, 1) + substr($digit, 1, 1);
      $mysum += $digit;
    }
    $rem = $mysum % 10;
    //if remainder is string, just a way to convert to integer by adding 0
    $rem = $rem + 0;
    if ($rem == 0) {
      $checkdigit = 0;
    } else {
      $checkdigit = 10 - $rem;
    }
    return $checkdigit;
  }

  // setup variables
  $user 	=& JFactory::getUser();
  // ##TODO: tmp added a 6 digit id from joomla user id
  $numDigits = 7;
  $userId = $user->get('id');
  $userPin = sprintf("%0".$numDigits."d",$userId);
  $bpayRef = $userPin . mod10($userPin);

  // ## TODO: Bpay biller code yet to be finalised
  $billerCode = '135194';

?>


<div id="bettaWrap" >
  <div class="moduletable">
    <h3 class="bpayhdr">DEPOSIT BY BPAY&reg;</h3>
    <div class="innerWrap">
      <div id="bpay">
        <div id="bpaylogo">&nbsp;</div>
        <div id="bpayrefwrap">
          <div class="bpayref">Biller Code:&nbsp;<?php echo $billerCode; ?><br />Ref:&nbsp;<?php echo $bpayRef; ?></div>
        </div>
        <div class="clr"></div>
          <div class="bpaytxt1">Telephone & Internet Banking &#8212; BPAY&#174;</div>
          <div class="bpaytxt2">Contact your bank, credit union or building society to make this payment from your cheque, savings, debit or credit card account. More info: www.bpay.com.au</div>
        <div class="clr"></div>
      </div>
    </div>
  </div>
</div>





