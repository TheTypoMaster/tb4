<?php // no direct access
defined('_JEXEC') or die('Restricted access');

// set the open state of the accordion
// default = -1 (all closed)
$accShowVar = JRequest::getVar( 'type', -1, 'get' );
switch ($accShowVar) {
	case 'moneybookers':
        $accShowVal =  0;
        break;
    case 'paypal':
        $accShowVal =  1;
        break;
    case 'bank':
        $accShowVal =  2;
        break;
    case -1:
        $accShowVal =  -1;
      break;
    default:
        $accShowVal =  -1;
      break;
}
?>

<script type="text/javascript">
    window.addEvent('domready', function(){
      var accordion = new Accordion('div.atStart1', 'div.atStart2', {
        show: <?php echo $accShowVal; ?>,
        opacity: true,
        alwaysHide: true,
        onActive: function(toggler, element){
          toggler.getElement('.Aarrow').setStyle('background-position', '0 -48px');
          //toggler.addClass('todaysRacesTypeHeadUp');
          //toggler.removeClass('todaysRacesTypeHeadDown');
        },
        onBackground: function(toggler, element){
          toggler.getElement('.Aarrow').setStyle('background-position', '-50px -48px');
          //toggler.addClass('todaysRacesTypeHeadDown');
          //toggler.removeClass('todaysRacesTypeHeadUp');
        }
      }, $('bettaWrap'));

    });
</script>

<div id="bettaWrap" >
  <div class="moduletable">
    <h3>Make A Withdrawal</h3>
    <div class="innerWrap" >
    <!-- Start MoneyBookers -->
          <div class="toggler atStart1 accordHead">
        <div class="Alogo"><div class="Lmoneybookers">&nbsp;</div></div>
        <div class="Atitle">MoneyBookers</div>
        <div class="Aarrow">&nbsp;</div>
      </div>
      <div class="element atStart2">
        <div class="accordBody">
          <div class="accForm">
            <h5>Make a MoneyBookers Withdrawal</h5>
            <form class="acconutFrm" method="post" action="/index.php?option=com_payment&amp;c=withdrawal&amp;task=withdraw">
              <label for="moneybookers_email">MoneyBookers Login / Email</label>
              <input type="text" name="moneybookers_email" value="<?php echo isset($this->formData['moneybookers_email']) ? htmlspecialchars($this->formData['moneybookers_email']) : ''; ?>" />
              <?php echo isset($this->formErrors['moneybookers_email']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_email']) . '</div>' : ''; ?>
              <label for="moneybookers_amount">Amount to Withdraw&nbsp;(AUD $)</label>
              <input type="text" name="moneybookers_amount" value="<?php echo isset($this->formData['moneybookers_amount']) ? htmlspecialchars($this->formData['moneybookers_amount']) : '' ; ?>" />
              <?php echo isset($this->formErrors['moneybookers_amount']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_amount']) . '</div>' : '' ; ?>
              <br />
              <p>Please note: All withdrawals are processed in Australian Dollars</p>
              <input type="hidden" name='withdrawalType' value='moneybookers' />
              <input type="hidden" name='itemid' value='<?php echo htmlspecialchars($this->itemid) ?>' />
              <input type="submit" name="moneybookers_withdrawal" class="bigBluButt" value="Withdraw to MoneyBookers" onclick="this.disabled=true; this.form.submit();" />
            </form>
          </div>
          <div class="accInfo">
            <h5>Sign up for MoneyBookers</h5>
            <div class="moneybookersLogo">&nbsp;</div>
            <p>Whether you are making payments, sending or receiving money online, with Skrill (Moneybookers) you only need an email address and a password. No need to carry all your payment details around and repeatedly type them in.<br />
            <br />
            <br />
            <a href="http://www.moneybookers.com/app/consumer.pl" target="_blank">Click here to learn more about MoneyBookers.</a></p>

            <!-- <div class="accInfo_paypal">
              <div class="plsnote">
                <div class="plsHdr">PLEASE NOTE:</div>
                MoneyBookers withdrawals are only available if you have MoneyBookers Account</a>.
              </div>
            </div>  -->
          </div>
          <div class="clr"></div>
        </div>
      </div>  
    <!-- End MoneyBooker -->
    <div class="hrzspr"></div>
    
      <div class="toggler atStart1 accordHead">
        <div class="Alogo"><div class="Lpaypal">&nbsp;</div></div>
        <div class="Atitle">PayPal</div>
        <div class="Aarrow">&nbsp;</div>
      </div>
      <div class="element atStart2">
        <div class="accordBody">
          <div class="accForm">
            <h5>Make a PayPal Withdrawal</h5>
            <form class="acconutFrm" method="post" action="/index.php?option=com_payment&amp;c=withdrawal&amp;task=withdraw">
              <label for="paypal_email">PayPal Login / Email</label>
              <input type="text" name="paypal_email" value="<?php echo isset($this->formData['paypal_email']) ? htmlspecialchars($this->formData['paypal_email']) : ''; ?>" />
              <?php echo isset($this->formErrors['paypal_email']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_email']) . '</div>' : ''; ?>
              <label for="paypal_amount">Amount to Withdraw&nbsp;(AUD $)</label>
              <input type="text" name="paypal_amount" value="<?php echo isset($this->formData['paypal_amount']) ? htmlspecialchars($this->formData['paypal_amount']) : '' ; ?>" />
              <?php echo isset($this->formErrors['paypal_amount']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_amount']) . '</div>' : '' ; ?>
              <br />
              <p>Please note: All withdrawals are processed in Australian Dollars</p>
              <input type="hidden" name='withdrawalType' value='paypal' />
              <input type="hidden" name='itemid' value='<?php echo htmlspecialchars($this->itemid) ?>' />
              <input type="submit" name="paypal_withdrawal" class="bigBluButt" value="Withdraw to PayPal" onclick="this.disabled=true; this.form.submit();" />
            </form>
          </div>
          <div class="accInfo">
            <h5>Sign up for PayPal</h5>
            <div class="paypalLogo">&nbsp;</div>
            <p>Paypal is the safer, easier way to pay and get paid online.<br />
            <br />
            The service allows anyone to pay in any way they prefer, including through credit cards, bank accounts, buyer credit or account balances, without sharing personal financial information.<br />
            <br />
            <a href="https://cms.paypal.com/au/cgi-bin/marketingweb?cmd=_render-content&content_ID=microsites_apac/How_does_PayPal_work&nav=1.0.0" target="_blank">Click here to learn more about PayPal.</a></p>

            <div class="accInfo_paypal">
              <div class="plsnote">
                <div class="plsHdr">PLEASE NOTE:</div>
                PayPal withdrawals are only available if you have provided your <a href="index.php?option=com_content&view=article&id=3&Itemid=11" target="_blank">Identification Document</a>.
              </div>
            </div>
          </div>
          <div class="clr"></div>
        </div>
      </div>
      <div class="hrzspr"></div>


		<?php if($this->show_bankdeposit): ?>
      <div class="toggler atStart1 accordHead">
        <div class="Alogo"><div class="Lbank">&nbsp;</div></div>
        <div class="Atitle">Bank Account</div>
        <div class="Aarrow">&nbsp;</div>
      </div>
      <div class="element atStart2">
        <div class="accordBody">
          <div class="accForm">
            <h5>Make a Bank Account Withdrawal</h5>
            <form class="acconutFrm" method="post" action="/index.php?option=com_payment&amp;c=withdrawal&amp;task=withdraw">
              <label for="bank_amount">Amount to Withdraw&nbsp;(AUD $)</label>
              <input type="text" name="bank_amount" value="<?php echo isset($this->formData['bank_amount']) ? htmlspecialchars($this->formData['bank_amount']) : '';?>" />
              <?php echo isset($this->formErrors['bank_amount']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['bank_amount']) . '</div>' : ''  ?>
              <input type="hidden" name='withdrawalType' value='bank' />
              <input type="hidden" name='itemid' value='<?php echo htmlspecialchars($this->itemid) ?>' />
              <input type="submit" name="bank_withdrawal" class="bigBluButt" value="Withdraw to My Bank Account" />
            </form>
          </div>
          <div class="accInfo">
            <h5>&nbsp;</h5>
            <div class="plsnote">
              <div class="plsHdr">PLEASE NOTE:</div>
              Bank Account withdrawals are only available if you have provided your <a href="index.php?option=com_content&view=article&id=3&Itemid=11" target="_blank">Identification Document</a>.
            </div>
          </div>
          <div class="clr"></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

