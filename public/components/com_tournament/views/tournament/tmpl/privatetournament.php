<?php // no direct access
defined('_JEXEC') or die('Restricted access');

?>
<html>
	<head>
		<script src="/media/system/js/mootools.js" type="text/javascript"></script>
		<script type="text/javascript" src="/templates/topbetta/js/general.js"></script>
		<script src="components/com_tournament/assets/common.js" type="text/javascript"></script>
		<link type="text/css" href="/templates/topbetta/css/template.css" rel="stylesheet">
		<style>
		.clr-all{
			clear: both;
		}
		.error-text{
			height: 12px !important;
		}
		.lightbox-form-full-width label{
			margin-bottom: 0px !important;
		}
		</style>
		<!--[if lte IE 8]>
		<style>
		.label-left{
			margin-left: 5px !important;
			margin-right: 5px !important;
		}
		.error-text{
			height: 18px !important;
		}
		*{
			overflow: hidden;
		}
		</style>
		<![endif]-->

		<script language="javascript">
			window.addEvent('domready', function() {
					$('sport_option').addEvent('change', function(e) {
						var sport_id = $('sport_option').getProperty('value');
						var complete_trigger = "var competition_id = $('competition_option').getProperty('value');loadOptions('index.php?option=com_tournament&task=ajaxcall&type=eventgroup&competition_id='+competition_id,'event_group_option', false);";
						loadOptions('/index.php?option=com_tournament&task=ajaxcall&type=competition&sport_id='+sport_id, 'competition_option', false, complete_trigger);
					});

					$('competition_option').addEvent('change', function(e) {
						var competition_id = $('competition_option').getProperty('value');
						loadOptions('index.php?option=com_tournament&task=ajaxcall&type=eventgroup&competition_id='+competition_id,'event_group_option', false);
					});

					prizeFormatCheck();
					$('buyin_option').addEvent('change', function(e) {
						prizeFormatCheck();
					});
					//for ie 6 and ie 7
					$('prize_format_option').addEvent('change', function(e) {
						prizeFormatCheck();
					});
			});

			function prizeFormatCheck() {
				selected_buyin = $('buyin_option').getProperty('value');
				is_free_buyin = ($('buyin_' + selected_buyin).getText() == 'FREE');
				winner_takes_all_id = null;
				$ES('option', 'prize_format_option').each(function(e){
					is_winner_takes_all = (e.getText() == 'Winner Takes All');
					if(is_winner_takes_all) {
						winner_takes_all_id = 'prize_format_' + e.getProperty('value');
					}
					if(is_free_buyin && !is_winner_takes_all) {
						e.setProperty('disabled', 'disabled');
						//for ie 6 and ie 7
						e.setStyle('color', '#c9c9c9');
						e.setProperty('title','This option is only available for non-free tournaments.');
					} else {
						e.removeProperty('disabled');
						e.setStyle('color', '');
						e.setProperty('title','');
					}
				});
				if(is_free_buyin && winner_takes_all_id) {
					$(winner_takes_all_id).setProperty('selected', 'selected');
				}
			}
		</script>
	</head>
	<body style="min-width:0;">
	<div class="private-tournament-box-content">
		<div class="lightbox-title"><img src="templates/topbetta/images/create-private-tournament.png" border="0" alt=""/></div>
		<div class="create-tourn-wrap">
			<form class="create-tourn-form" action="/index.php" name="create-private-tournament" method="post">
				<div class="lightbox-form-split clr">
					<label class="label-left" style= "text-align:left;">
	                    SPORT:
	                        <select id="sport_option" name="sport_id">
	                    	<?php foreach($this->sport_options as $sport_id => $sport_name) :?>
	                            <option value="<?php echo $this->escape($sport_id) ?>"<?php echo $sport_id == $this->selected_sport ? ' selected="selected"' : ''?>><?php echo $this->escape($sport_name) ?></option>
							<?php endforeach ?>
	                        </select>
	                        <?php $sport_err = isset($this->formerrors['sport_id']) ? $this->formerrors['sport_id'] : null ?>
	                        <span class="error-text<?php echo $sport_err ? ' error-lightbox' : ''?>"><?php echo $this->escape($sport_err)?></span>
	                    </label>

	                    <label class="label-right">
	                    BUY-IN:
	                        <select id="buyin_option" name="buyin_id">
	                        <?php foreach($this->buyin_options as $buyin_id => $buyin_label) :?>
	                            <option id="buyin_<?php echo $this->escape($buyin_id) ?>" value="<?php echo $this->escape($buyin_id) ?>"<?php echo $buyin_id == $this->selected_buyin ? ' selected="selected"' : '' ?>><?php echo $this->escape($buyin_label)?></option>
	                        <?php endforeach ?>
	                        </select>
	                        <?php $buyin_err = isset($this->formerrors['buyin_id']) ? $this->formerrors['buyin_id'] : null ?>
	                        <span class="error-text<?php echo $buyin_err ? ' error-lightbox' : ''?>"><?php echo $this->escape($buyin_err)?></span>
	                        </label>
	                    </div>

		                <div class="lightbox-form-split clr-all">
		                    <label class="label-left">
		                    COMPETITION:
		                        <select id="competition_option" name="competition_id">
		                        <?php foreach($this->competition_options as $competition_id => $competition_name) :?>
		                            <option value="<?php echo $this->escape($competition_id) ?>"<?php echo $competition_id == $this->selected_competition ? ' selected="selected"' : '' ?>><?php echo $this->escape($competition_name)?></option>
		                        <?php endforeach ?>
		                        </select>
		                        <?php $competition_err = isset($this->formerrors['competition_id']) ? $this->formerrors['competition_id'] : null ?>
		                        <span class="error-text<?php echo $competition_err ? ' error-lightbox' : ''?>"><?php echo $this->escape($competition_err)?></span>
		                    </label>

		                    <label class="label-right">
		                    PRIZE FORMAT:
		                        <select id="prize_format_option" name="prize_format_id">
		                        <?php foreach($this->prize_format_options as $prize_format_id => $prize_format_name) :?>
		                            <option id="prize_format_<?php echo $this->escape($prize_format_id) ?>" value="<?php echo $this->escape($prize_format_id) ?>"<?php echo $prize_format_id == $this->selected_prize_format ? ' selected="selected"' : '' ?>><?php echo $this->escape($prize_format_name)?></option>
		                        <?php endforeach ?>
		                        </select>
		                        <?php $prize_format_err = isset($this->formerrors['prize_format_id']) ? $this->formerrors['prize_format_id'] : null ?>
		                        <div class="error-text<?php echo $prize_format_err ? ' error-lightbox' : ''?>"><?php echo $this->escape($prize_format_err) ?></div>
		                    </label>

		                </div><!--
 						<div class="lightbox-form-full-width error-text<?php echo isset($this->formerrors['prize_format_id']) ? ' error-lightbox' : ''?>"><?php echo $this->escape($this->formerrors['prize_format_id'])?></div>

		                --><div class="lightbox-form-full-width  clr-all">
		                    <label>
		                    EVENT:
		                        <select id="event_group_option" name="event_group_id">
		                        <?php foreach($this->event_group_options as $event_group_id => $event_group_name) :?>
		                            <option value="<?php echo $this->escape($event_group_id) ?>"<?php echo $event_group_id == $this->formdata['event_group_id'] ? ' selected="selected"' : '' ?>><?php echo $this->escape($event_group_name)?></option>
		                        <?php endforeach ?>
		                        </select>
		                        <?php $event_group_err = isset($this->formerrors['event_group_id']) ? $this->formerrors['event_group_id'] : null ?>
		                        <span class="error-text<?php echo $event_group_err ? ' error-lightbox' : ''?>"><?php echo $this->escape($event_group_err)?></span>
		                    </label>
		                </div>

		                <div class="lightbox-form-full-width  clr-all">
		                <label>GIVE YOUR TOURNAMENT A NAME: <input type="text" class="tournament-name" name="tournament_name" value="<?php echo isset($this->formdata['tournament_name']) ? $this->escape($this->formdata['tournament_name']) : '' ?>"/></label>
		                <?php $tournament_name_err = isset($this->formerrors['tournament_name']) ? $this->formerrors['tournament_name'] : null ?>
		                <span class="error-text<?php echo $tournament_name_err ? ' error-lightbox' : ''?>"><?php echo $this->escape($tournament_name_err)?></span>
		                </div>

		                <div class="lightbox-form-full-width">
			                <label class="tourn-require-pass"> <input type="checkbox" class="checkbox-require-pass" name="required_password"<?php echo isset($this->formdata['required_password']) && $this->formdata['required_password'] ? ' checked="checked"' : '' ?>/> <span class="req-password-text">Requires a password to access this tournament</span></label>
			                <?php $required_password_err = isset($this->formerrors['required_password']) ? $this->formerrors['required_password'] : null ?>
			                <div class="clr-all error-text<?php echo $required_password_err ? ' error-lightbox' : ''?>"><?php echo $this->escape($required_password_err)?></div>
		                </div>

		                <div class="lightbox-form-full-width clr-all">
			                <label><span class="req-password-title">PASSWORD:</span> <input type="text" class="private-tourn-pass" name="password" value="<?php echo isset($this->formdata['password']) ? $this->escape($this->formdata['password']) : '' ?>"/></label>
			                <?php $password_err = isset($this->formerrors['password']) ? $this->formerrors['password'] : null ?>
			                <?php $general_err = isset($this->formerrors['general']) ? $this->formerrors['general'] : null ?>
			                <div class="error-text <?php echo ($password_err) || $general_err ? ' error-lightbox' : ''?>">
			                	<?php echo $this->escape($password_err)?>
			                	<?php echo $this->escape($general_err) ?>
			                </div>
		                </div>

		                <input type="submit" class="create-private-tourn-submit"  name="private-tourn-submit" value="Create Private Tournament!"/>
		                <input type="hidden" name="component" value="com_tournament" />
		                <input type="hidden" name="task" value="registerprivatetournament" />
		                <input type="hidden" name="from_tournament_id" value="<?php echo $this->escape($this->formdata['from_tournament_id']) ?>" />

			            <div class="lightbox-bottom-links">
			            	<a href="/content/article/9" target="_blank">Private Tournament Help</a>
			            </div>
		             </form>
	        </div>
		</div>
	</body>

</html>







