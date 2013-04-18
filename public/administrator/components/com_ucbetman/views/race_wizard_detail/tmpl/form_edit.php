<?php
/**
 * @version $Id: component.php 5173 2006-09-25 18:12:39Z Jinx $
 * @package Joomla
 * @subpackage Config
 * @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 * 
 * php echo $lang->getName();  
 */
defined('_JEXEC') or die('Restricted access');

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');

$nowTime = strtotime('now');
?>

<script language="javascript" type="text/javascript">

	function submitbutton(pressbutton){
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform(pressbutton);
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert("<?php echo JText::_( 'race_wizard item must have a name', true ); ?>");
			
		}
		else {
			submitform(pressbutton);
		}
	}	
</script>

<style type="text/css">
	table.paramlist td.paramlist_key {
		width: 92px;
		text-align: left;
		height: 30px;
	}
</style>

<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">
<div class="col50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Race Tournament Details' ); ?></legend>

		<table class="admintable">
	
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="tournName">
					<?php echo JText::_( 'Tournament ID' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="id" id="id" size="32" maxlength="250" value="<?php echo $this->detail->tournID; ?>" />
			</td>
		</tr>	
		<tr>	
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="tournament_name">
					<?php echo JText::_( 'Tournament Name' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="tournament_name" id="tournament_name" size="32" maxlength="250" value="<?php echo $this->detail->tournament_name; ?>" />
			</td>
		</tr>	
		<tr>
			
		<td width="100" align="right" class="key">
				<label for="tab_meeting_id">
					<?php echo JText::_( 'Race Meeting Code' ); ?>:
				</label>
			</td>
			<td>
				 <input disabled class="text_area" type="text" name="tab_meeting_code" id="tab_meeting_code" size="32" maxlength="250" value="<?php echo $this->detail->tab_meeting_id; ?>" />
			</td>
						
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="tournament_value">
					<?php echo JText::_( 'Tournament Value' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="tournament_value" id="tournament_value" size="32" maxlength="250" value="<?php echo $this->detail->tournament_value; ?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="gameplay">
					<?php echo JText::_( 'Game Play' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="gameplay" id="gameplay" size="32" maxlength="250" value="<?php echo $this->detail->game_play; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="betlimit_wple">
					<?php echo JText::_( 'Bet Limit - Win/Place/EachWay' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="betlimit_wple" id="betlimit_wple" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_wple; ?>" />
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
				<label for="prizeFormula">
					<?php echo JText::_( 'Prize/Payout Formula' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="prizeFormula" id="prizeFormula" size="32" maxlength="250" value="<?php echo $this->detail->prizeFormula; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="betlimit_t">
					<?php echo JText::_( 'Bet Limit - Trifecta' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="betlimit_t" id="betlimit_t" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_t; ?>" />%
			</td>
		</tr>
		<tr>
						
			<td width="100" align="right" class="key">
				<label for="parentID">
					<?php echo JText::_( 'Jackpot Parent' ); ?>:
				</label>
			</td>
			
			<td>
				<input class="text_area" type="text" name="parentID" id="parentID" size="10" maxlength="10" value="<?php echo $this->detail->parentID; ?>" />
			</td>
			
			<td width="100" align="right" class="key">
				<label for="betlimit_q">
					<?php echo JText::_( 'Bet Limit - Quinella' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="betlimit_q" id="betlimit_q" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_q; ?>" />%
			</td>
			
		</tr>
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="starting_bbucks">
					<?php echo JText::_( 'Starting Bucks' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="starting_bbucks" id="starting_bbucks" size="10" maxlength="15" value="<?php echo $this->detail->starting_bbucks; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="betlimit_f">
					<?php echo JText::_( 'Bet Limit - Flexi' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="betlimit_f" id="betlimit_f" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_f; ?>" />%
			</td>
		</tr>
		
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="min_prize_pool">
					<?php echo JText::_( 'Minimum Prize Pool' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="min_prize_pool" id="min_prize_pool" size="10" maxlength="10" value="<?php echo $this->detail->min_prize_pool; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="betlimit_e">
					<?php echo JText::_( 'Bet Limit - Exacta' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="betlimit_e" id="betlimit_e" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_e; ?>" />%
			</td>
		</tr>
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="tournInfo">
					<?php echo JText::_( 'Tournament Information' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="tournInfo" id="tournInfo" size="128" maxlength="250" value="<?php echo $this->detail->tournInfo; ?>" />
			</td>
		</tr>
		
			
		<tr>
			<td valign="top" align="right" class="key">
				<label for="ordering">
					<?php echo JText::_( 'ORDERING' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['ordering']; ?>
			</td>
		</tr>

		</table>
	</fieldset>


<div class="clr"></div>

<input type="hidden" name="cid[]" value="<?php echo $this->detail->tournID; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="race_wizard_detail" />
</form>


