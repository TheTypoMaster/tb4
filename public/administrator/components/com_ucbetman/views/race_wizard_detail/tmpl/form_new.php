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

// format combo boxes

	for ($i=0, $n=count( $this->meetings ); $i < $n; $i++) {
		$row = &$this->meetings[$i];
		$date = $raceDate = date("d/m/y", $row->date);
		$meetingsArray[$row->id] =  $row->tab_meeting_id.' - '.$row->name. ' - '.$date.'';
	}
	$meetingKey = "";
	$select = '<select id="meeting" name="meeting">';
	$select .= "\t<option value=''>\n";
	foreach ($meetingsArray as $key => $val) {
	    $select .= "\t<option value=\"".$key."\"";
	    if ($key == $meetingKey) {
	        $select .= " selected>".$val."\n";
	    } else {
	        $select .= ">".$val."\n";
	    }
	}
	$select .= "</select>";



	for ($i=0, $n=count( $this->tournparents ); $i < $n; $i++) {
		$row = &$this->tournparents[$i];
		$tournArray[$row->id] =  $row->date .' - '. $row->name. ' - '.  $row->tab_meeting_id.'';
	}
	$tournKey = "";
	$tournselect = '<select id="tournparent" name="tournparent">';
	$tournselect .= "\t<option value=''>\n";
	if ($tournArray){
		foreach ($tournArray as $key => $val) {
		    $tournselect .= "\t<option value=\"".$key."\"";
		    if ($key == $tournKey) {
		        $tournselect .= " selected>".$val."\n";
		    } else {
		        $tournselect .= ">".$val."\n";
		    }
		}
	}
	$tournselect .= "</select>";



	for ($i=0, $tn=count( $this->tournvalues ); $i < $tn; $i++) {
		$row = &$this->tournvalues[$i];
		$tournVArray[$row->tournament_value] = 'BuyIn $'.$row->tournament_value .' + Entry Fee $'.$row->buy_in;
	}
	$tournVKey = "";
	$tournVselect = '<select id="tournvalues" name="tournvalues">';
	$tournVselect .= "\t<option value=''>\n";
	foreach ($tournVArray as $key => $val) {
	    $tournVselect .= "\t<option value=\"".$key."\"";
	    if ($key == $tournKey) {
	        $tournVselect .= " selected>".$val."\n";
	    } else {
	        $tournVselect .= ">".$val."\n";
	    }
	}
	$tournVselect .= "</select>";
  

?>

<script language="javascript" type="text/javascript">
	
	function loopSelected()
	{
	  // var txtSelectedValuesObj = document.getElementById('txtSelectedValues');
	  var selectedArray = new Array();
	  var selObj = $("tournvalues");
	  var i;
	  var count = 0;
	  for (i=0; i<selObj.options.length; i++) {
	    if (selObj.options[i].selected) {
	      selectedArray[count] = selObj.options[i].value;
	      count++;
	    }
	  }
	  return selectedArray;
	}

	
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.name.value == ""){
			alert( "<?php echo JText::_( 'race_wizard item must have a name', true ); ?>" );
		
		} else {
			submitform( pressbutton );
		}
	}
	
	window.addEvent('domready', function(){
		// build up POST to createPC controller function
		$('createPCButton').addEvents({
			'click': function(e) {
				var meetingID = $("meeting").value;
				var meetingCode = $("meetingCode").value;
				var tournValue = loopSelected();
				var gamePlay = $("gameplay").value ;
				var startBucks = $("startbucks").value;
				var meetingCode = $("meetingCode").value;
				var tournParent = $("tournparent").value;
				// var autoCreateNew = $("autoCreateNew").value;
				var tournInfo = $("tournInfo").value;
				var prizeFormula = $("prizeFormula").value;
				var minPrizePool = $("minPrizePool").value;
				var tournName = $("tournName").value;
				var meetingName = $("meetingName").value;
				var blWPE = $("blWPE").value;
				var blT = $("blT").value;
				var blQ = $("blQ").value;
				var blF = $("blF").value;
				var blE = $("blE").value;

				var pcurl = 'index.php?option=com_ucbetman&controller=race_wizard_detail&task=createPC';
				pcurl += '&meetingID='+ meetingID;
				pcurl += '&buyin='+ tournValue;
				pcurl += '&gameplay='+ gamePlay;
				pcurl += '&startbucks='+ startBucks;
				pcurl += '&tournparent=' + tournParent;
				// pcurl += '&autoCreateNew=' + autoCreateNew;
				pcurl += '&tournInfo=' + tournInfo;
				pcurl += '&prizeFormula=' + prizeFormula;
				pcurl += '&minPrizePool=' + minPrizePool;
				pcurl += '&meetingcode=' + meetingCode;
				pcurl += '&tournname=' + tournName;
				pcurl += '&blWPE=' + blWPE;
				pcurl += '&blT=' + blT;
				pcurl += '&blQ=' + blQ;
				pcurl += '&blF=' + blF;
				pcurl += '&blE=' + blE;
				pcurl += '&meetingCode=' + meetingCode;
				pcurl += '&meetingName=' + meetingName;
				
				new Event(e).stop();
				alert(pcurl);
				// Check validation
				if (tournName) {
					if (meetingID || meetingCode) {
						if (tournValue) {
								if (startBucks) {
									window.location = pcurl;
								} else {
									alert('You need to enter Starting Bucks');
								}
						} else {
							alert('You need to enter Tournament Buy IN value.');
						}
					} else {
						alert('You need to select a Meeting from the list OR manually enter a TAB Meeting Code.');
					}
				}else {
					alert('You need to give the tournament a name.');
				}
			}
	    });
	}); 
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
					<?php echo JText::_( 'Tournament Name' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="tournName" id="tournName" size="32" maxlength="250" value="" />
			</td>
					<td width="100" align="right" class="key">
				<label for="tab_meeting_id">
					<?php echo JText::_( 'Future TAB Meeting Code eg. MR_20091217:' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="meetingCode" id="meetingCode" size="32" maxlength="250" value="" />
			</td>
		</tr>	
		<tr>
			
		<td width="100" align="right" class="key">
				<label for="tab_meeting_id">
					<?php echo JText::_( 'Select Upcoming Race Meeting:' ); ?>:
				</label>
			</td>
			<td>
				 <?php echo $select; ?>
			</td>
	<td width="100" align="right" class="key">
				<label for="tab_meeting_name">
					<?php echo JText::_( 'Future Meeting Name' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="meetingName" id="meetingName" size="32" maxlength="250" value="" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="track">
					<?php echo JText::_( 'Tournament Value to Create' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $tournVselect; ?>
			</td>
			
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="track">
					<?php echo JText::_( 'Game Play' ); ?>:
				</label>
			</td>
			<td>
				<select id="gameplay" name="gameplay" >
					<option value="Single">Single</option>
					<option value="Jackpot">Jackpot</option>

				</select>
			</td>
			<td width="100" align="right" class="key">
				<label for="blWPE">
					<?php echo JText::_( 'Bet Limit - Win/Place/EachWay' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="blWPE" id="blWPE" size="10" maxlength="10" value="unlimited" />
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
				<label for="prizeFormula">
					<?php echo JText::_( 'Prize/Payout Formula' ); ?>:
				</label>
			</td>
			<td>
				<select id="prizeFormula" name="prizeFormula" >
					<option value="Cash">Cash</option>
					<option value="Tickets">Tickets</option>
				</select>
			</td>
			<td width="100" align="right" class="key">
				<label for="blT">
					<?php echo JText::_( 'Bet Limit - Trifecta' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="blT" id="blT" size="10" maxlength="10" value="unlimited" />%
			</td>
		</tr>
		<tr>
						
			<td width="100" align="right" class="key">
				<label for="track">
					<?php echo JText::_( 'Jackpot Parent' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $tournselect; ?>
			</td>
			<td width="100" align="right" class="key">
				<label for="blQ">
					<?php echo JText::_( 'Bet Limit - Quinella' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="blQ" id="blQ" size="10" maxlength="10" value="unlimited" />%
			</td>
			
		</tr>
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="startbucks">
					<?php echo JText::_( 'Starting Bucks' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="startbucks" id="startbucks" size="10" maxlength="15" value="1000" />
			</td>
			<td width="100" align="right" class="key">
				<label for="blF">
					<?php echo JText::_( 'Bet Limit - Flexi' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="blF" id="blF" size="10" maxlength="10" value="unlimited" />%
			</td>
		</tr>
		
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="minPrizePool">
					<?php echo JText::_( 'Minimum Prize Pool' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="minPrizePool" id="minPrizePool" size="10" maxlength="10" value="10" />
			</td>
			<td width="100" align="right" class="key">
				<label for="blE">
					<?php echo JText::_( 'Bet Limit - Exacta' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="blE" id="blE" size="10" maxlength="10" value="unlimited" />%
			</td>
		</tr>
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="tournInfo">
					<?php echo JText::_( 'Tournament Information' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="tournInfo" id="tournInfo" size="128" maxlength="250" value="Enter Information to be displayed on the tournament Information screen here" />
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

		<tr><td><button id="createPCButton" name="createPCButton" type="button" href="#">Create Tournament</button></td></tr>
		 
		</table>
	</fieldset>
</div>

<div class="clr"></div>

<input type="hidden" name="cid[]" value="<?php echo $this->detail->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="race_wizard_detail" />
</form>


