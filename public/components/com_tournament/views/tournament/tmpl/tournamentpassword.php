<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
    <div class="private-tournament-box-password">

    <div class="lightbox-title"><img src="/components/com_tournament/images/please-enter-tourn-password.png" border="0" alt=""/></div>
    	 <div class="enter-tourn-pass-wrap">

            <div class="lightbox-message-full">This tournament is password-protected. Please enter the password provided by the tournament provider.</div>
                <div class="lightbox-form-full-width">
                	<label><span class="req-password-title">PASSWORD:</span></label>
                	<input type="text" class="private-tourn-pass" name="private-tourn-pass" id="private-tourn-pass" />
					<div id="error-txt" style="visibility:hidden; text-align: center;" class="error-lightbox">Please enter the correct tournament password supplied to you</div>
                </div>

                <input type="submit" class="register-private-tourn"  name="btnMatchPassword" id="btnMatchPassword" value="Register for Private Tournament" onclick="javascript:matchTournamentPassword()"/>
				<input type="hidden" name="tourntament_id" id="tourntament_id" value="<?=$this->tournament_id?>" />
				<input type="hidden" name="tourntament_url" id="tourntament_url" value="<?=$this->tournament_url?>" />

            <div class="lightbox-bottom-links">
            	<a href="/content/article/9">Private Tournament Help</a>
            </div>

        </div>
    </div><!-- close sbox-content -->
