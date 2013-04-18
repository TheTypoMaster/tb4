<?php
defined('_JEXEC') or die('Restricted access');

$image_root = getcwd();
?>
	<div id="raceTable"> 
		<div class="todaysRaceDetailsRow"> 
			<table class="todaysRaceDetailsTbl" width="100%"> 
				<tr class="ContentTableHeader"> 
                	<td class="runnerNo"></td>
					<td class="runnerNo">No.</td> 
					<td class="runnerName">Runner</td> 
					<td class="jockey"><?php print $this->meeting->associate_label; ?></td> 
					<td class="one2tips">Betta Ratings</td> 
					<td class="winOdds">Win Approx.</td> 
					<td class="placeOdds">Place Approx.</td> 
					<td class="selBoxes sb1 sb2 sb3 sb4">1st/Box</td>
					<td class="selBoxes sb2 sb3 sb4">2nd</td>
					<td class="selBoxes sb3 sb4">3rd</td>
					<td class="selBoxes sb4">4th</td> 
				</tr>
				<?php $class = 1; ?>
				<?php foreach($this->runner_list as $runner) : ?>
				<tr class="ContentTableRow<?php echo $class . $runner->class; ?>"> 
                	<td class="runnerNo"><?php 
					if($this->meeting->icon=='greyhoundsIcon') { 
						echo (file_exists($image_root.'/rugs/'.$runner->number .'.png')) ? '<img src="/rugs/'.$runner->number . '.png" width="20" >' : '<img src="/rugs/default.png" width="20" >';
					} else {
						echo (file_exists($image_root.'/silks/'.$runner->silk_id .'.png')) ? '<img src="/silks/'.$runner->silk_id .'.png" width="20" >' : '<img src="/silks/default.png" width="20" >';
					} ?></td> 
					<td class="runnerNo"><?php echo $runner->number; ?></td> 
					<td class="runnerName"><?php echo $runner->name; ?></td> 
					<td class="jockey"><?php echo  $runner->associate; ?><?php echo (!empty($runner->weight)) ? ' <i>('.$runner->weight.'kgs)</i>' : ''; ?></td> 
					<td class="one2tips"> 
						<div class="one2tippr"> 
							<span class="hasTip" title="TopBetta Ratings calculated from over 15,000 serious punters"> 
                				<img src="components/com_tournament/images/trixel.gif" width="<?php echo $runner->rating_width; ?>px" height="16px" alt="" class="percentTip" /> 
							</span>
						</div> 
					</td>
					<td class="winOdds"><?php echo ($runner->enabled) ? JHTML::tooltip('Current approximate odds', '', '', ($runner->win_dividend) ? $runner->win_dividend : $runner->win_odds) : 'scr'; ?></td> 
					<td class="placeOdds"><?php echo ($runner->enabled) ? JHTML::tooltip('Current approximate odds', '', '', ($runner->place_dividend) ? $runner->place_dividend : $runner->place_odds) : 'scr'; ?></td> 
					<td class="selBoxes sb1"><input type="checkbox" class="firstA firstP secondP thirdP fourthP chkbox" name="selection[first][]" value="<?php echo $runner->id; ?>" <?php echo (!$runner->enabled || !$this->race->betting_open) ? ' disabled="disabled"' : '' ?> /></td> 
					<td class="selBoxes sb2"><input type="checkbox" class="secondA secondP thirdP fourthP chkbox" name="selection[second][]" value="<?php echo $runner->id; ?>"  /></td> 
					<td class="selBoxes sb3"><input type="checkbox" class="thirdA thirdP fourthP chkbox" name="selection[third][]" value="<?php echo $runner->id; ?>"  /></td> 
					<td class="selBoxes sb4"><input type="checkbox" class="fourthA fourthP chkbox" name="selection[fourth][]" value="<?php echo $runner->id; ?>"  /></td> 
				</tr> 
				<?php $class = ($class == 2) ? 1:2; ?>
				<?php endforeach; ?>
				<tr class="ContentTableRow<?php echo ($class == 2) ? 2:1; ?> selFieldRow"> 
					<td class="runnerNo">&nbsp;</td> 
					<td class="runnerNo">&nbsp;</td> 
					<td class="runnerName">&nbsp;</td> 
					<td class="jockey">&nbsp;</td> 
					<td class="one2tips">&nbsp;</td> 
					<td class="winOdds">&nbsp;</td> 
					<td class="placeOdds">Field</td> 
					<td class="selBoxes"><input type="checkbox" class="selectA firstP secondP thirdP fourthP chkbox" name="all" value="1"  /></td> 
					<td class="selBoxes"><input type="checkbox" class="selectA secondP thirdP fourthP chkbox" name="all" value="1"  /></td> 
					<td class="selBoxes"><input type="checkbox" class="selectA thirdP fourthP chkbox" name="all" value="1"  /></td> 
					<td class="selBoxes"><input type="checkbox" class="selectA fourthP chkbox" name="all" value="1"  /></td> 
				</tr> 
				<tr class="ContentTableRow<?php echo ($class == 2) ? 1:2; ?> selFieldRow"> 
					<td class="runnerNo">&nbsp;</td> 
					<td class="runnerNo">&nbsp;</td> 
					<td class="runnerName">&nbsp;</td> 
					<td class="jockey">&nbsp;</td> 
					<td class="one2tips">&nbsp;</td> 
					<td class="winOdds">&nbsp;</td> 
					<td class="placeOdds">&nbsp;</td> 
					<td class="selBoxes">&nbsp;</td> 
					<td>&nbsp;</td>
					<td>&nbsp;</td>					
					<!--<td class="selBoxes" id="flexi">Flexi</td> 
					<td class="selBoxes"><input type="checkbox" class="thirdP fourthP chkbox" name="flexi" value="1"  /> </td> -->
					<td class="selBoxes"><a href="/content/article/6" target="_blank"><span class="hasTip" title="Click here for information on bet types."><img src="templates/topbetta/images/icon-help.png" alt="help" border="0"/></span></a></td> 
				</tr> 
			</table> 
		</div> 
        <div class="tote-disclaimer">All tote odds should be considered approximate and are current to the best of our ability.</div> 
    <div class="tote-disclaimer">Betta Ratings from <a href="http://www.12follow.com.au" target="_blank">www.12follow.com.au</a></div> 
  </div> 
