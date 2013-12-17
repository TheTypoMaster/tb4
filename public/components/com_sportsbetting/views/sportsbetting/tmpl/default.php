<?php

defined('_JEXEC') or die('Restricted access');

//$sportsbettinglink = "/index.php?option=com_sportsbetting";
$sportsbettinglink = "/betting/sports";

$currentBT = 0;
if ($this->typesNoptions) {
	foreach ($this->typesNoptions AS $tnoRow) {
		if ($currentBT != $tnoRow->betType) { unset($optionsArr); }
		//$bodytag = str_replace("%body%", "black", "<body text='%body%'>");
		$betSelection = str_replace("/", " / ", $tnoRow->betSelection);
		$optionArr = array($tnoRow->selectionID,$betSelection,$tnoRow->odds,$tnoRow->bet_place_ref,$tnoRow->bet_type_ref,$tnoRow->external_selection_id);
		$optionsArr[] = $optionArr;
		$typesNoptionsArr[$tnoRow->betType] = $optionsArr;
		$currentBT = $tnoRow->betType;
	}
} else {
	$typesNoptionsArr = '';
}


/**
 * Modifies a string to remove al non ASCII characters and spaces.
 */
function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
 
    // trim
    $text = trim($text, '-');
  
    // lowercase
    $text = strtolower($text);
 
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
 
    if (empty($text)) {
        return 'n-a';
    }
 
    return $text;
}

?>

<div class="sportsbettingWrap">
	<div class="sportsbettingHead">
		<div id="TBlogo"></div>
	</div>
	
	<div id="SBaccordion" class="sportsbettingLeft">
		<?php
			$currentSport = '';
			if ($this->sportsNcomps) {
				foreach ($this->sportsNcomps as $sportNcomp) {
					//$linkvars = '&sid='.$sportNcomp->sportID.'&cid='.$sportNcomp->eventGroupId.'&eid=0';
					$linkvars = '/'.slugify($sportNcomp->sportName).'/event/'.$sportNcomp->sportID.'/'.$sportNcomp->eventGroupId;
					
					if ($sportNcomp->sportID==$this->sportID) {
						$sActive = "Active";
						$sportName = $sportNcomp->sportName;
					} else { $sActive = ""; }
					if ($sportNcomp->sportName != $currentSport) {
						if($currentSport) {
							echo '</div>';
						}
						echo '<div class="accordToggler sportButt'.$sActive.'">'.$sportNcomp->sportName.'<span class="toggArr"></span></div><div class="accordElement sEl'.$sActive.'">';
					}
					if ($sportNcomp->eventGroupId==$this->compID) {
						$cActive = "Active";
						$compName = $sportNcomp->name;
					} else { $cActive = ""; }
					echo '<p class="compButt'.$cActive.'"><a href="'.$sportsbettinglink.$linkvars.'">'.$sportNcomp->name.'</a></p>';
					$currentSport = $sportNcomp->sportName;
				}
				echo '</div>';
			} else {
				echo '<div class="sportButt">No Sports Data Available</div>';
			}
			
		?>
	</div>
	
	<div class="sportsbettingMain">
		<div class="eventButtsWrap">
			<div class="prevWrap"><div id="evPrev" class="fredPrev unselectable"></div></div>
			<div class="eventButts">
				<div id="eventFred" class="slider">
				<?php
					$cnt=1;					
					foreach ($this->events as $event) {
						$extEventName=0;
						$linkvars = '/'.slugify($sportName).'/'.slugify($event->eventName).'/'.$this->sportID.'/'.$this->compID.'/'.$event->eventID;
						if ($event->eventID==$this->eventID) {
							$eActive = "Active";
							$tid = $cnt;
							if ($tid % 4 == 0) {
								$adj = 3;
							} else {
								$adj = ($tid % 4) - 1;
							}
							$eventButtStart = $tid - $adj - 1;//set the active event
							$extEventName = 'id="evID'.$event->extEventId.'"';
							$eventName = $event->eventName;
						} else { $eActive = ""; }
						//$e_date = date("(d/m)", $event->eventStartTime);
						echo '<div class="slide eventButton'.$eActive.'" '.$extEventName.'><a href="'.$sportsbettinglink.$linkvars.'"><span class="e-date">'.date("(d/m)", strtotime($event->eventStartTime)).'</span> '.$event->eventName.'</a></div>';
						$cnt++;
					}
				?>
				</div>
			</div>
			<div class="nextWrap"><div id="evNext" class ="fredNext"></div></div>
		</div>

		<div class="betTypeButtsWrap">
			<div class="prevWrap"><div id="btPrev" class="fredPrev unselectable"></div></div>
			<div class="betTypeButts">
				<div id="bettypeFred" class="slider">
				<?php
					if ($typesNoptionsArr) {
						$bt=0;
						foreach ($typesNoptionsArr as $type => $options) {
							$bt == 0 ? $btActive = " typeButtonActive" : $btActive = "";
							echo '<div id="btButt'.$bt.'" class="slide typeButton'.$btActive.'"><span>'.$type.'</span></div>';
							$bt++;
						}
					} else {
						echo '<div id="btButt-1" class="slide"><span>No Bet Types Available</span></div>';
					}
				?>
				</div>
			</div>
			<div class="nextWrap"><div id="btNext" class="fredNext"></div></div>
		</div>

		<div class="betOptionsWrap">
			<?php
				if ($typesNoptionsArr) {
					$g=0;
					foreach ($typesNoptionsArr as $options) {
						$g == 0 ? $hideclass='' : $hideclass='hideGroup';
						//echo "xxx:".$options;
			?>
					<div id="boGroup<?php echo $g; ?>" class="betOptionsGroup <?php echo $hideclass; ?>">
						<?php foreach ($options as $option) { ?>
						<div id="selRow<?php echo $option[0]; ?>" class="betOptionsRow">
							<div class="betOptionLabel"><?php echo $option[1]; ?></div>
							<div class="betOptionOdds"><?php echo $option[2]; ?></div>
								<input class="betReference" type="hidden" name="betRef" value="<?php echo $option[3]; ?>" />
								<input class="betTypeRef" type="hidden" name="betTypeRef" value="<?php echo $option[4]; ?>" />
								<input class="betSelReference" type="hidden" name="betSelRef" value="<?php echo $option[5]; ?>" />
							<div><div class="betOptionBetButt">
								<a href="#bettixPopWrap" id="pbButt<?php echo $option[0]; ?>" class="place_bet">Bet</a>
							</div></div>
							<div><div href="#" class="betOptionMultiButt">ADD TO MULTI</div></div>
							<div class="clear"></div>
						</div>
						<?php } ?>
					</div>
			<?php
						$g++;
					}
				} else {
					echo '<div class="betOptionsRow">No Selection Data Available</div>';
				}
			?>
		</div>

		<div class="MultiBetWrap">
			
			<div class="MultiBetHeader">Multi-Bet Ticket</div>
			
			<!--
			<div class="MultiBetRow">
				<div class="MultiBetRemove"></div>
				<div class="MultiBetLabel"></div>
				<div class="MultiBetLabel"></div>
			</div>

			<div class="MultiBetTotals">
				<div class="MultiBetClearAll"></div>
				<div class="MBtotalsOdds"></div>
				<div class="MultiPlaceBetButt"></div>
			</div>
			-->
			
		</div>

	</div>
	<div class="clear"></div>



	<!-- NEW place bet ticket -->
	<div id="betTicket" class="reveal-modal">
		<div id="bettixPopWrap">
			<form action="/index.php" name="atpBetForm" id="sportsBetForm" method="POST">
				<input type="hidden" name="option" value="com_sportsbetting" />
				<input type="hidden" name="task" value="savebet" />
				<input type="hidden" name="id" value="???" />
				<input type="hidden" id="betRef" name="bet_place_ref" value="" />
				<input type="hidden" id="extSelID" name="external_selection_id" value="" />
				<input type="hidden" id="betTypeRef" name="bet_type_ref" value="" />
				<!--<input type="hidden" id="betAmount" name="bet_amount" value="" />
				<input type="hidden" id="betOdds" name="bet_odds" value="" /> -->
				<input type="hidden" name="sid" value="<?php echo $this->sportID; ?>" />
				<input type="hidden" name="cid" value="<?php echo $this->compID; ?>" />
				<input type="hidden" name="eid" value="<?php echo $this->eventID; ?>" />
				<input type="hidden" id="eventButtStart" value="<?php echo $eventButtStart; ?>" />
				<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
				<div class="top-header">Live Sports Bet Ticket</div>
				<div class="bettixPanel">
					<div class="bettixLoader">
						<div id="floatingCirclesG">
							<div class="f_circleG" id="frotateG_01"></div>
							<div class="f_circleG" id="frotateG_02"></div>
							<div class="f_circleG" id="frotateG_03"></div>
							<div class="f_circleG" id="frotateG_04"></div>
							<div class="f_circleG" id="frotateG_05"></div>
							<div class="f_circleG" id="frotateG_06"></div>
							<div class="f_circleG" id="frotateG_07"></div>
							<div class="f_circleG" id="frotateG_08"></div>
						</div>
						<div class="bettixLoaderText">Processing Bet...</div>
					</div>
					<div class="bettixNotice">
						<div class="bettixNoticeText"></div>
					</div>
					
					<div class="bettixBalances">
						<div class="bettixAccBalance">Account Balance: $ <span id="AccBal"></span></div>
						<div class="bettixFreeBalance">Free Credit: $ <span id="FreeBal"></span></div>
						<div class="clear"></div>
					</div>
					<div class="bettixFreeCredits">
						<div class="bettixCreditWrap">
							<div class="bettixCreditCheck">
								<div class="bettixCreditCheckLabel">YES</div>
								<div class="bettixCreditCheckBox"></div>
								<div class="clear"></div>
							</div>
							<div class="bettixCreditTextOn">
								Use $<span id="FCval"></span> of my FREE Credit
							</div>
							<div class="bettixCreditTextOff">Don't use my FREE Credit</div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="bettixAmntButts">
						<div class="bettixAmntRow">
							<div class="bettixAmntButt">$1</div>
							<div class="bettixAmntButt">$2</div>
							<div class="bettixAmntButt">$5</div>
							<div class="bettixAmntButt">$10</div>
							<div class="bettixAmntButt">$20</div>
						</div>
						<div class="bettixAmntRow">
							<div class="bettixAmntButt">$40</div>
							<div class="bettixAmntButt">$50</div>
							<div class="bettixAmntButt">$100</div>
							<div class="bettixAmntButt">$200</div>
							<div class="bettixAmntButt">$500</div>
						</div>
						<div class="clear"></div>
						<div class="bettixAmntRow">
							<div class="bettixAmntLabel">Select Bet Buttons Above<br>OR Enter Amount Here >></div>
							<div class="bettixAmntLabel2">$</div>
							<input id="betTicketAmount" class="betTicketAmount" type="text" name="betvalue" size="20" value="0" />
							<div class="bettixAmntReset">Reset Bet Amount</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="bettixDisplay">
							<!-- <div class="bettixBetCompName"><?php echo "$compName"; ?></div> -->
							<div class="bettixBetEventName"><?php echo "$eventName"; ?></div>
							<div id="bettixBetTypeName"></div>
							<div id="bet_selection"></div>
							<div id="bettixAmountType">$<span id="betAmount"></span> at <span id="betOdds"></span> to win $<span id="toWinAmnt"></span></div>
							<div class="bettixButtsWrap">
								<div id="cancelBets" class="bettixAmntCancel close-reveal-modal">Cancel Bet</div>
								<div id="placeBets" class="bettixPlaceBet">Place Your Bet</div>
								<div id="confirmBets" class="bettixConfirmBet">Confirm Your Bet</div>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
					</div>
					<div class="bettixOkButtWrap">
						<div class="bettixOkButt">Ok</div>
					</div>
					
					<div class="clear"></div>
				</div>
			</form>
			<div class="clear"></div>
			<a class="close-reveal-modal closeModalTop">&#215;</a>
		</div>
	</div>





</div>





<?php

//print_r($this->typesNoptions);
print_r($this->events);

//echo "<br><br>";
//print_r($typesNoptionsArr);

//print_r($this->sportsNcomps);

echo "<br><br>";
//echo "sportID: ".$this->sportID."<br>";
//echo "compID: ".$this->compID."<br>";
//echo "eventID: ".$this->eventID."<br>";

//echo "xxx: ".$this->sportsNcomps[0]->eventGroupId;



?>



