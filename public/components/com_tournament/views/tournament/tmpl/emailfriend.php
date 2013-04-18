<?php // no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="private-tournament-box-content">

    <div class="lightbox-title"><img src="/components/com_tournament/images/promote-your-private-tournament.png" border="0" alt=""/></div>
	<div style="text-align:center; font-weight: bold;">You should only email friends, family, colleagues, and people you know.</div>
    	 <div class="promote-tourn-wrap">
        	<form class="promote-tourn-form" action="/index.php" name="prmote-private-tournament" onsubmit="javascript:return validateEmailForm();">

            	<div class="lightbox-form-split">
                    <div class="promote-tourn-input-info">
                    Select From Your Previous Private Tournaments:
                    </div>

                    <label class="label-right">
                        <select id="previous_tournament" name="previous_tournament">
                        	<option value="-1"></option>
                        	<?php
                        	if(!empty($this->previous_tournament)){
                        	foreach($this->previous_tournament as $previous_tournament){
                        	?>
                            	<option value="<?=$previous_tournament->tournament_id?>"><?=$previous_tournament->name?> - <?=date("d/m/y",strtotime($previous_tournament->start_date))?></option>
                            <?php
                        		}
                        	}?>
                        </select>
                    </label>
                </div>

                <div class="lightbox-form-split">
                    <div class="promote-tourn-input-info">
                    Add Additional Email Addresses:<br/>(1 per line)
                    <div id="error-email" class="error-lightbox" style="visibility:hidden"> </div>
                    </div>
                    <textarea rows="4" cols="26" class="promote-tourn-textarea" name="tournament_private_emails" id="tournament_private_emails" onclick="javascript:toggleInitialTxt(this,'Enter email addresses')" onblur="javascript:toggleInitialTxt(this,'Enter email addresses',1)">Enter email addresses</textarea>

                </div>

                <div class="lightbox-form-full-width">
                    <label>Customise Email Greeting:
                       <textarea rows="6" cols="20" class="promote-tourn-textarea-full" id="tournament_email_content" name="tournament_email_content">Hello!

I’ve just created my own Private Tournament on TopBetta.com and I’m inviting you to enter.

Registering on TopBetta is free and you can win cash prizes.

Just follow the simple steps below.

Cheers,

<?=$this->user->name?></textarea>
					</label>
                    <label>Email Information:</label>
                    <div class="private-tourn-email-output"><?=$this->private_tournament_promo_email_text?></div>
                </div>



                <div class="lightbox-form-full-width">
                	<label class="padTop"> <input type="checkbox" class="checkbox-require-pass" id="tournament_friends_over_18_chk" name="tournament_friends_over_18_chk"/> <span class="req-password-text">My friends are over 18 and I understand the terms &amp; conditions </span>

                    </label>
                     <div id="error-content" class="error-lightbox" style="visibility:hidden">You must ensure your friends are over 18</div>
                </div>

                <input type="submit" class="promote-private-tourn-submit"  name="private-tourn-submit" value="Create Private Tournament!"/>
				<input type="hidden" name="tournament_id" value="<?=$this->tournament_id?>" />
				<input type="hidden" name="component" value="com_tournament" />
				<input type="hidden" name="task" value="sendPrivateTournamentPromoEmail" />
            </form>

            <div class="lightbox-bottom-links">
            	The Terms: The email will be sent from YOU. Your friends must be over 18 to enter.
            	<a href="/content/article/2" target="_blank">Terms &amp; Conditions</a>
            </div>

        </div>
    </div><!-- close sbox-content -->
