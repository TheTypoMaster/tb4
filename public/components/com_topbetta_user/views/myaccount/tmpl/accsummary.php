<?php 
/**
* @package sportman01
* @version 1.1
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); 

$startbalance = number_format($this->myBets[StartBalance], 2, '.', ',');
$endBalance = number_format($this->myBets[ClosingBalance], 2, '.', ',');
$unresultedBetAmount = number_format($this->myBets[UnresultedBetAmount], 2, '.', ',');

$todayDate = date( 'Y-m-d', strtotime('today 00:00'));
$yestDate = date( 'Y-m-d', strtotime('-1 day'));
//TODO: yest date clickr
?>
<script>
</script>

<div id="bettaWrap" >
	<div class="moduletable">
		<h3>LIVE BETS - ACCOUNT SUMMARY</h3>
		<div class="innerWrap">
			<div class="dispDate">
				<div class="balHdr">DATE:</div>
				<div class="balAmnt"><?php echo $this->betDate; ?></div>
			</div>
			<div class="selectDate">
				<a href="index.php?option=com_ucbetman&view=myaccount&layout=mybets&betsDate=<?php echo $todayDate; ?>&Itemid=71">Show Today</a> | 
				<a href="index.php?option=com_ucbetman&view=myaccount&layout=mybets&betsDate=<?php echo $yestDate; ?>&Itemid=71">Show Yesterday</a>
			</div>

 			<div id="betSlctr" class="openBal">
               <select id="dobd" class="dobd" name="dobd">
                	<option value="">-Day-</option>
					<option value="01">01</option>
                    <option value="02">02</option>
                    <option value="03">03</option>
                    <option value="04">04</option>

                    <option value="05">05</option>
                    <option value="06">06</option>
                    <option value="07">07</option>
                    <option value="08">08</option>
                    <option value="09">09</option>
                    <option value="10">10</option>

                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>

                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                    <option value="22">22</option>

                    <option value="23">23</option>
                    <option value="24">24</option>
                    <option value="25">25</option>
                    <option value="26">26</option>
                    <option value="27">27</option>
                    <option value="28">28</option>

                    <option value="29">29</option>
                    <option value="30">30</option>
                    <option value="31">31</option>
                </select>
                <select id="dobm" class="dobm" name="dobm">
                    <option value="">&nbsp;-- Month --&nbsp;</option>
                    <option value="01">January</option>

                    <option value="02">Febuary</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>

                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <select id="doby" class="doby" name="doby">
                    <option value="">- Year -&nbsp;</option>
                    <option value="2009">2009</option>
                    <option value="2010">2010</option>
				</select>
				<button type="button">VIEW BETS</button>
			</div>
			<div class="clr"></div>

			<div class="openBal">
				<div class="balHdr">Opening Balance:</div>
				<div class="balAmnt">$<?php echo $startbalance; ?></div>
			</div>

			<div class="clr"></div>
			<table class='myLiveBets' width='100%'>
				<tbody>
					<tr>
						<th>TICKET No.</th>
						<th>TIME</th>
						<th class="lft">DESCRIPTION</th>
						<th>BET VALUE</th>
						<th>BET TYPE</th>
						<th>PLACE</th>
						<th>RETURN</th>
						<th>BALANCE</th>
					</tr>
					<?php
					$Balance = $startbalance;
					if ($this->myBets[Bets][Bet]) {
						//$result = array_reverse($input);
						$myBetsArr = array_reverse($this->myBets[Bets][Bet]);
						
						$myBetsArr[Amount] ? $only1bet = 1 : $only1bet = 0;
						$only1bet ? $numofbets = 1 : $numofbets = count($myBetsArr);
						for ($i=0, $n=$numofbets; $i < $n; $i++) {
						//foreach ($myBetsArr as $myBet){
							($only1bet) ? $myBet = &$myBetsArr : $myBet = &$myBetsArr[$i];
							$rownum = ($i % 2) ? "2" : "1";
	
							//format time
							$pos = strrpos($myBet[BetTime], "T");
							$formatedtime = substr($myBet[BetTime], ($pos+1), 5);
							
							//$myBet[BetTime];
							$betAmount = number_format($myBet[Amount], 2, '.', ',');
							$ReturnAmount = number_format($myBet[ReturnAmount], 2, '.', ',');
							if($ReturnAmount > 0 && $myBet[Lose] == 0) {
								$resultClass = 'mbWon';
								$betAmount = number_format($myBet[Invest], 2, '.', ',');
							} elseif($ReturnAmount == 0 && $myBet[Lose] > 0) {
								$resultClass = 'mbLoss';
								$betAmount = number_format($myBet[Invest], 2, '.', ',');
								$ReturnAmount = 'NIL';
							} elseif($ReturnAmount == 0 && $myBet[Placing] == 99) {
								$resultClass = '' ;
								$betAmount = number_format($myBet[Invest], 2, '.', ',');
								$ReturnAmount = 'SCR';
							} else {
								$resultClass = '' ;
								$ReturnAmount = '-';
							}
							$Balance = number_format($myBet[Balance], 2, '.', ',');
							//$Balance = $Balance - $myBet[Amount];
							
							// place anything that needs to be removed from desc. into searchFor array
							$searchFor = array('Harness ', 'Galloping ', 'Greyhound ');
							$Description = str_replace($searchFor, "", $myBet[Description]);
						?>
							<tr class="ContentTableRow<?php echo $rownum; ?> <?php echo $resultClass; ?>">
								<td><?php echo $myBet[BetID]; ?></td>
								<td><?php echo $formatedtime; ?></td>
								<td class="lft"><?php echo $Description; ?></td>
								<td class="rgt"><?php echo $betAmount; ?></td>
								<td><?php echo $myBet[BetType]; ?></td>
								<td><?php echo $myBet[Placing]; ?></td>
								<td class="rgt"><?php echo $ReturnAmount; ?></td>
								<td class="rgt"><?php echo $Balance; ?></td>
							</tr>
					<?php
						}; 
					} else {;
					?>
							<tr>
								<td colspan="8" class="nobets">You have not placed any bets today.</td>
							</tr>					
					<?php } ?>
				</tbody>
			</table><br/>
			<div class="unresulted">
				<div class="balHdr">Unresulted:</div>
				<div class="balAmnt">($<?php echo $unresultedBetAmount; ?>)</div>
			</div>
			<div class="closeBal">
				<div class="balHdr">Closing Balance:</div>
				<div class="balAmnt">$<?php echo $endBalance; ?></div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
</div>
