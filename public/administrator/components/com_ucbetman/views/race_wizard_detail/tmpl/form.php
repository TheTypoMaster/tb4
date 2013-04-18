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
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.name.value == ""){
			alert( "<?php echo JText::_( 'atp_wizard item must have a name', true ); ?>" );
		
		} else {
			submitform( pressbutton );
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
		<legend><?php echo JText::_( 'DETAILS' ); ?></legend>

		<table class="admintable">
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="tab_meeting_id">
					<?php echo JText::_( 'Meeting ID' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="tab_meeting_id" id="tab_meeting_id" size="32" maxlength="250" value="<?php echo $this->detail->tab_meeting_id;?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="name">
					<?php echo JText::_( 'Meeting Name' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="name" id="name" size="32" maxlength="250" value="<?php echo $this->detail->name;?>" />
			</td>
		</tr>
		
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="events">
					<?php echo JText::_( 'Number of Events' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="events" id="events" size="32" maxlength="250" value="<?php echo $this->detail->events;?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="type">
					<?php echo JText::_( 'Meeting Type' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="type" id="type" size="32" maxlength="250" value="<?php echo $this->detail->type;?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="track">
					<?php echo JText::_( 'Track Rating' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="track" id="track" size="32" maxlength="250" value="<?php echo $this->detail->track;?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="weather">
					<?php echo JText::_( 'Weather' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="weather" id="weather" size="32" maxlength="250" value="<?php echo $this->detail->weather;?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="date">
					<?php echo JText::_( 'Meeting Date' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="date" id="date" size="32" maxlength="250" value="<?php echo $this->detail->date;?>" />
			</td>
		</tr>
		
		<tr>
			<td valign="top" align="right" class="key">
				<?php echo JText::_( 'PUBLISHED' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
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
</div>

<div class="clr"></div>

<input type="hidden" name="cid[]" value="<?php echo $this->detail->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="atp_wizard_detail" />
</form>


