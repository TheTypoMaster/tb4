<?php // @version $Id: default_form.php 11215 2008-10-26 02:25:51Z ian $
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
  function validateForm( frm ) {
    var valid = document.formvalidator.isValid(frm);
    if (valid == false) {
      // do field validation
      if (frm.email.invalid) {
        alert( "<?php echo JText::_( 'Please enter a valid e-mail address.', true );?>" );
      } else if (frm.text.invalid) {
        alert( "<?php echo JText::_( 'CONTACT_FORM_NC', true ); ?>" );
      }
      return false;
    } else {
      frm.submit();
    }
  }
</script>

<div class="moduletable">
          <h3>Enter Your Contact Details & Message</h3>
                   <div class="formcontent">


<div id="contactForm">

<form action="<?php echo JRoute::_('/contact-us'); ?>" class="form-validate" method="post" name="emailForm" id="emailForm">

<fieldset id="usrDetails">
  <div class="contimg">
  <p><b>Phone:</b><br />
Australia 1300 886 503<br />
International 61 2 49 574 704<br />
</p>
<p>
Fax: <br />
Australia 02 49 574 702<br />
International 61 2 49 574 702<br />
</p>
<p>
Postal address:<br />
Po Box 188 <br />
Lambton NSW 2299<br />
</p>
<p>
Address:<br />
83 Regent St<br />
New Lambton NSW 2305<br />
</p>

  </div>
  <div class="firstFormBit contact_email<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
    <label for="contact_name">
    <?php echo JText::_( 'Enter your name' ); ?>:</label>
    <input type="text" name="name" id="contact_name" size="30" class="inputbox" value="<?php echo $this->user->name; ?>" />
  </div>
  <div class="contact_email<?php echo  $this->params->get( 'pageclass_sfx' ); ?>">
    <label id="contact_emailmsg" for="contact_email">
    <?php echo JText::_( 'Email address' ); ?>*:</label>
    <input type="text" id="contact_email" name="email" size="30" value="<?php echo $this->user->email; ?>" class="inputbox required validate-email" maxlength="100" >
  </div>
  <div class="contact_email<?php echo  $this->params->get( 'pageclass_sfx' ); ?>"><label for="contact_subject">
    <?php echo JText::_( 'Message subject' ); ?>:</label>
    <input type="text" name="subject" id="contact_subject" size="30" class="inputbox" value="" />
  </div>
    <div class="contact_email<?php echo $this->params->get( 'pageclass_sfx' ); ?>"><label id="contact_textmsg" for="contact_text" class="textarea">
    <?php echo JText::_( 'Enter your message' ); ?>*:</label>
    <textarea name="text" id="contact_text" class="inputbox required" rows="10" cols="40"></textarea>
  </div>

  <?php if ($this->contact->params->get( 'show_email_copy' )): ?>
  <div class="contact_email_checkbox<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
    <input type="checkbox" name="email_copy" id="contact_email_copy" value="1"  />
    <label for="contact_email_copy" id="copy_email_label" class="copy">
      <?php echo JText::_( 'EMAIL_A_COPY' ); ?>
    </label>
  <div class="clr"></div>
  </div>
  <?php endif; ?>

  <button class="button validate" type="submit"><?php echo JText::_('Send Message'); ?></button>
  <input type="hidden" name="view" value="contact" />
  <input type="hidden" name="user_id" value="<?php echo $this->user->id; ?>" />
  <input type="hidden" name="id" value="<?php echo $this->contact->id; ?>" />
  <input type="hidden" name="task" value="submit" />
  <?php echo JHTML::_( 'form.token' ); ?>

</fieldset>

</form>

</div>
</div>
</div>
