<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="col width-100 edit-user">

  <form action="index.php" method="post" name="adminForm" id="adminForm">
    <fieldset class="adminform">
      <legend>User Information</legend>
        <table class='admintable' id="user_info_left">
          <tr>
            <td class='key'>Username</td>
            <td>
              <input type="text" name="username" value="<?php echo $this->escape($this->formData['username'] ) ?>" />
              <?php echo $this->formErrors['username'] ? '<div class=\'error\'>' . $this->escape($this->formErrors['username']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Title</td>
            <td>
              <select name="title">

                <option value=''>Select ...</option>
<?php foreach ($this->options['title'] as $k => $v ) : ?>
	<?php $selected = ($k == $this->formData['title'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($k)?>"><?php echo $this->escape($v)?></option>
<?php endforeach; ?>
              </select>
              <?php echo $this->formErrors['title'] ? '<div class=\'error\'>' . $this->escape($this->formErrors['title']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">First Name</td>
            <td>
              <input type="text" name="first_name" value="<?php echo $this->escape($this->formData['first_name'] ) ?>" />
              <?php echo $this->formErrors['first_name'] ? '<div class=\'error\'>' . $this->escape($this->formErrors['first_name']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class='key'>Last Name</td>
            <td>
              <input type='text' name="last_name" value="<?php echo $this->escape($this->formData['last_name'] ) ?>" />
              <?php echo $this->formErrors['last_name'] ? '<div class=\'error\'>' . $this->escape($this->formErrors['last_name']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Date of Birth</td>
            <td>
              <select name="dob_day">

                <option value="">Select ...</option>
<?php foreach ($this->options['day'] as $k => $v ) : ?> 
	<?php $selected = ($k == $this->formData['dob_day'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($k)?>"><?php echo $this->escape($v)?></option>
<?php endforeach; ?>
              </select>

              <select name="dob_month">

                <option value="">Select ...</option>
<?php foreach ($this->options['month'] as $k => $v ) : ?>
	<?php $selected = ($k == $this->formData['dob_month'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($k)?>"><?php echo $this->escape($v)?></option>
<?php endforeach; ?>
              </select>

              <select name="dob_year">

                <option value="">Select ...</option>
<?php foreach ($this->options['year'] as $k => $v ) : ?>
	<?php $selected = ($k == $this->formData['dob_year'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($k)?>"><?php echo $this->escape($v)?></option>
<?php endforeach; ?>
              </select>
              <?php echo $this->formErrors['dob'] ? '<div class="error">' . $this->escape($this->formErrors['dob']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Mobile Number</td>
            <td>
              <input type="text" name="mobile" value="<?php echo $this->escape($this->formData['mobile'] ) ?>" />
              <?php echo $this->formErrors['mobile'] ? '<div class="error">' . $this->escape($this->formErrors['mobile']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Home Number</td>
            <td>
              <input type="text" name="phone" value="<?php echo $this->escape($this->formData['phone'] ) ?>" />
              <?php echo $this->formErrors['phone'] ? '<div class="error">' . $this->escape($this->formErrors['phone']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Email</td>
            <td>
              <input type="text" name="email" value="<?php echo $this->escape($this->formData['email'] ) ?>" />
              <?php echo $this->formErrors['email'] ? '<div class="error">' . $this->escape($this->formErrors['email']) . '</div>'  : ''?>
            </td>
          </tr>

          <tr>
            <td class="key">Password</td>
            <td>
              <input type="password" name="password" value="<?php echo $this->escape($this->formData['password'] ) ?>" />
              <?php echo $this->formErrors['password'] ? '<div class="error">' . $this->escape($this->formErrors['password']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Confirm Password</td>
            <td>
              <input type="password" name="password2" value="<?php echo $this->escape($this->formData['password2'] ) ?>" />
              <?php echo $this->formErrors['password2'] ? '<div class="error">' . $this->escape($this->formErrors['password2']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">&nbsp;</td>
            <td>
              <input type="checkbox" name="change_password" id="change_password" <?php echo $this->escape($this->formData['change_password'] ? ' checked="checked"' : '' ) ?> />
              <label for="change_password">Tick the box to change password</label>
              <?php echo $this->formErrors['change_password'] ? '<div class="error">' . $this->escape($this->formErrors['change_password']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Street Address</td>
            <td>
              <input type="text" name="street" value="<?php echo $this->escape($this->formData['street'] ) ?>" />
              <?php echo $this->formErrors['street'] ? '<div class="error">' . $this->escape($this->formErrors['street']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Suburb / City</td>
            <td>
              <input type="text" name="city" value="<?php echo $this->escape($this->formData['city'] ) ?>" />
              <?php echo $this->formErrors['city'] ? '<div class="error">' . $this->escape($this->formErrors['city']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">State</td>
            <td>
              <select name="state">
                <option value="">Select ...</option>
<?php foreach ($this->options['state'] as $k => $v) : ?>
	<?php $selected = ($k == $this->formData['state'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($k)?>"><?php echo $this->escape($v)?></option>
<?php endforeach; ?>
              </select>
            </td>
          </tr>

          <tr>
            <td class="key">Country</td>
            <td>
              <select name="country">
                <option value="">Select ...</option>
<?php foreach ($this->country_list as $code => $country) : ?>
	<?php $selected = ($code == $this->formData['country'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($code)?>"><?php echo $this->escape($country['name'])?></option>
<?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td class="key">Postcode</td>
            <td>
              <input type="text" name="postcode" value="<?php echo $this->escape($this->formData['postcode']) ?>" />
              <?php echo $this->formErrors['postcode'] ? '<div class=\'error\'>' . $this->escape($this->formErrors['postcode']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
            <td class="key">Heard About Us</td>
            <td>
              <select name="heard_about">
                <option value=''>Select ...</option>
<?php foreach ($this->options['heard_about'] as $k => $v) : ?>
	<?php $selected = ($k == $this->formData['heard_about'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($k)?>"><?php echo $this->escape($v)?></option>
<?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td class="key">Additional Information</td>
            <td>
              <textarea name="heard_about_info" style="width: 300px; height: 150px;" ><?php echo( $this->escape($this->formData['heard_about_info']) ) ?></textarea>
              <?php echo $this->formErrors['heard_about_info'] ? '<div class="error">' . $this->escape($this->formErrors['heard_about_info']) . '</div>'  : ''?>
            </td>
          </tr>
           <tr>
            <td class="key">Marketing Opt-in</td>
            <td>
              <input type="checkbox" name="marketing" id="marketing" <?php echo $this->escape($this->formData['marketing'] ? ' checked="checked"' : '' ) ?>' />
              <label for="marketing">Tick the box to recieve marketing emails</label>
            </td>
          </tr>
          <tr>
            <td class="key">Jackpot Reminder Emails</td>
            <td>
              <input type="checkbox" name="jackpot_reminder" id="jackpot_reminder" <?php echo $this->escape($this->formData['jackpot_reminder'] ? ' checked="checked"' : '' ) ?>'  value="1"/>
              <label for="jackpot_reminder">Tick the box to recieve Jackpot tournament betting open emails</label>
            </td>
          </tr>
          <tr>
            <td class="key">User Status</td>
            <td>
              <select name="status">
                <option value="">Select ...</option>
<?php foreach ($this->options['status'] as $k => $v) : ?>
	<?php $selected = ($k == $this->formData['status'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($k)?>"><?php echo $this->escape($v)?></option>
<?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td class="key">Identity Verified</td>
            <td>
              <input type="checkbox" name="identity_verified_flag" id="identity_verified_flag" value="1" <?php echo $this->escape($this->formData['identity_verified_flag'] ? ' checked="checked"' : '' ) ?> />
              <label for="identity_verified_flag">Check this once the identity of a user has been verified.</label>
              <div class="identity_doc">
              	Primary Doc Type:
				<select name="identity_doc">
                <option value=''>Select ...</option>
<?php foreach ($this->options['identity_doc'] as $k => $v) : ?>
	<?php $selected = ($k == $this->formData['identity_doc'] ? 'selected ="selected"' : ''); ?>
              <option <?php echo $selected ?> value="<?php echo $this->escape($k)?>"><?php echo $this->escape($v)?></option>
<?php endforeach; ?>
				</select>
              </div>
              <div class="identity_doc">
              	Primary Doc ID:
              	<input type="text" name="identity_doc_id" value="<?php echo $this->escape($this->formData['identity_doc_id'] ) ?>" />
              </div>
            </td>
          </tr>
          <tr>
            <td class="key">BSB Number</td>
            <td>
              <input type="text" name="bsb_number" id="bsb_number" value="<?php echo $this->escape($this->formData['bsb_number'] ) ?>" />
              <label for="bsb_number">Customer&rsquo;s BSB number</label>
            </td>
          </tr>
          <tr>
            <td class="key">Bank Account Number</td>
            <td>
              <input type="text" name="bank_account_number" id="bank_account_number" value="<?php echo $this->escape($this->formData['bank_account_number'] ) ?>" />
              <label for="bank_account_number">Customer&rsquo;s bank account number</label>
            </td>
          </tr>
          <tr>
            <td class="key">Account Name</td>
            <td>
              <input type="text" name="account_name" id="account_name" value="<?php echo $this->escape($this->formData['account_name'] ) ?>" />
              <label for="bank_account_number">Customer&rsquo;s bank account name</label>
            </td>
          </tr>
          <tr>
            <td class="key">Bank Name</td>
            <td>
              <input type="text" name="bank_name" id="bank_name" value="<?php echo $this->escape($this->formData['bank_name'] ) ?>" />
              <label for="bank_account_number">Customer&rsquo;s bank name</label>
            </td>
          </tr>
          <tr>
            <td class="key">Source</td>
            <td>
              <input type="text" name="source" id="source" value="<?php echo $this->escape($this->formData['source'] ) ?>" />
            </td>
          </tr>
          <tr>
            <td class="key">Exclusion Date</td>
            <td>
              <input type="text" name="self_exclusion_date" id="self_exclusion_date" value="<?php echo $this->escape($this->formData['self_exclusion_date'] ) ?>" /> YYYY-MM-DD HH:MM:SS
              <?php echo $this->formErrors['self_exclusion_date'] ? '<div class=\'error\'>' . $this->escape($this->formErrors['self_exclusion_date']) . '</div>'  : ''?>
            </td>
          </tr>
        </table>
        <table class='admintable' width="350" id="table #user_info_right">
          <tr>
          	<td colspan="2">
              Account Balance:<?php echo '$' . $this->escape(number_format($this->account_balance/100, '2', '.', ','))?> | Tournament Dollars Balance:<?php echo '$' . $this->escape(number_format($this->tournament_balance/100, '2', '.', ','))?>
            </td>
          </tr>
          <tr>
            <td>Bet Limit</td>
            <td>
              <input type="text" name="bet_limit" id="bet_limit" value="<?php echo $this->escape($this->formData['bet_limit'] ) ?>" />
              <input type="checkbox" name="no_limit" id="no_limit"<?php echo $this->formData['no_limit'] ? ' checked="checked"' : '' ?> />
              <label for="no_limit">No Limit</label>
              <?php echo $this->formErrors['bet_limit'] ? '<div class=\'error\'>' . $this->escape($this->formErrors['bet_limit']) . '</div>'  : ''?>
            </td>
          </tr>
          <tr>
          	<td colspan="2">Note: users are required to <strong>wait 7 days</strong> after requesting to raise their limit.</td>
          </tr>
          <?php if (!empty($this->bet_limit_request_list)) : ?>
          <tr>
          	<td colspan="2">
          		HISTORY:<br />
          		<table id="bet_limit_history" cellpadding="0" cellspacing="0">
          		<?php foreach ($this->bet_limit_request_display as $bet_limit_request) : ?>
          		<tr<?php echo $bet_limit_request->row_class; ?>>
          			<td><?php echo $this->escape($bet_limit_request->operationer) ?></td>
          			<td><?php echo $this->escape($bet_limit_request->update_date) ?></td>
          			<td><?php echo $this->escape($bet_limit_request->value) ?></td>
          			<td><?php echo $this->escape($bet_limit_request->action) ?></td>
          		</tr>
          		<?php endforeach; ?>
          		</table>
          	</td>
          </tr>
          <?php endif; ?>
        </table>
        <div class="clr"></div>
        <input type="hidden" name="recipient" id="transaction_recipient" value="<?php echo $this->escape($this->formData['recipient']); ?>" />
        <input type="hidden" name="option" value="<?php echo JRequest::getVar( 'option' ); ?>" />
        <input type="hidden" name="c" value="<?php echo JRequest::getVar( 'c' ); ?>" />
        <input type="hidden" name="user_id" value="<?php echo $this->user->user_id; ?>" />
        <input type="hidden" name="task" value="" />
      </fieldset>
    </form>
</div>
<?php if(isset($this->formErrors['formError'])) : ?>
	<?php echo '<div class="error">' .  $this->formErrors['formError'] . '</div>'; ?>
<?php endif; ?>