//com_sportsbetting javascript

//jQuery functions
( function($) {
	$(function(){

		//setup event butts sliders
		// carouFredSel test
		var eventButtStart = $('#eventButtStart').val();
		$("#eventFred").carouFredSel({
			auto        : false,
		    circular    : false,
		    infinite    : false,
		    width   	: "100%",
		    align   	: "left",
		    items		: {
		    	start: parseInt(eventButtStart)
		    },
		    scroll		: {
		    	items: 4
		    },
		    prev : "#evPrev",
		    next : "#evNext",
			swipe       : {
				onTouch     : true,
				onMouse     : true
			}
		});
		console.log("start: " + eventButtStart);


		//setup bet type butts slider
		$("#bettypeFred").carouFredSel({
			auto        : false,
		    circular    : false,
		    infinite    : false,
		    align   	: "left",
		    items		: {
		    	start: $('div.typeButtonActive')
		    },
		    prev : "#btPrev",
		    next : "#btNext",
			swipe       : {
				onTouch     : true,
				onMouse     : true
			}
		});


		// bet types active highlighting
		$('.typeButton').click(function() {
			var buttGrpId = $(this).attr("id");
			var buttGrp = buttGrpId.slice(buttGrpId.lastIndexOf("btButt")+6);
			//active highlighting
			$(".betTypeButts div div").removeClass("typeButtonActive");
			$("#"+buttGrpId).addClass("typeButtonActive");
			
			$(".betOptionsGroup").addClass("hideGroup");
			$("#boGroup"+buttGrp).removeClass("hideGroup");

		});


		// bet ticket modal popup
		$('a.place_bet').click(function(e) {
			e.preventDefault();
			var betChoice = $(this);
			
			console.log(TB.apiUrl);

			//check user is logged in else
			//display the error message
			var TBapiUrl = TB.apiUrl;
			$.get(TBapiUrl + 'method=getAccBalances', function(obj) {

				//chk if method returns ok				
				if (obj.status == 200) {

					TB.TBuser = obj.tb_user;
					TB.funds = obj.funds;
					//calc Total Funds and floor
					TB.fundsTotal = TB.funds['account_balance'] + TB.funds['tournament_dollars'];
					TB.fundsTotal2bet = Math.floor(TB.fundsTotal);
					//console.log("total funds: " + TB.fundsTotal);
				
					if (TB.TBuser == 1) {
						if (TB.fundsTotal2bet > 0) {
		
							//set the acount balance display
							 //number_format(1234.56, 2, '.', ' ');
							$('#AccBal').text(number_format(TB.funds['account_balance'], 2, '.', ','));
							$('#FreeBal').text(number_format(TB.funds['tournament_dollars'], 2, '.', ','));
						
							// get the ticket values
							var pbButtID = betChoice.attr('id').substr( 6 );
							var betType = $('div.typeButtonActive').text();
							var betSelection = $('#selRow'+pbButtID+' div.betOptionLabel').text();
							var betSelRef = $('#selRow'+pbButtID+' input.betSelReference').val();
							var betPlaceRef = $('#selRow'+pbButtID+' input.betReference').val();
							var betTypeRef = $('#selRow'+pbButtID+' input.betTypeRef').val();
							var betOdds = $('#selRow'+pbButtID+' div.betOptionOdds').text();			
										
							// set the ticket values
							if (localStorage.betAmount > 0) {
								var betAmntShown = localStorage.betAmount;
							} else {
								var betAmntShown = 0;
							}
							$('#betTicketAmount').val(betAmntShown);
							$('#bettixBetTypeName').text(betType);
							$('#bet_selection').text(betSelection);
							$('#betRef').val(betPlaceRef);
							$('#betTypeRef').val(betTypeRef);
							$('#extSelID').val(betSelRef);
							$('#betOdds').text(betOdds);
							chkTotal();
				
							//setup the bet ticket panels
							$('.bettixBalances').css('background', '#f5f5f5');
							$('.bettixBalances div').css('color', '#555');
							$('.bettixLoader').hide();
							$('.bettixFreeCredits').hide();
							$('.bettixOkButtWrap').hide();
							$('.bettixNotice').hide();
							$('#confirmBets').hide();
							$('.bettixBalances').show();
							$('.bettixAmntButts').show();
							$('.bettixDisplay').show();
							$('.bettixDisplay').show();
							$('.bettixNotice').removeClass("errorMsg noticeMsg");
							$('#placeBets').css('display', 'inline-block');
							
							} else { //no balance
								displayError("You don't have enough money to place a bet.");
							}
							
					} else { //not full account
						displayError('You need to upgrade to a full account to place bets.');
					}
					
					//$('#betTicket').reveal();
				} else { //not logged in
					displayError(obj.error_msg);
				}
				$('#betTicket').reveal();
	
				//console.log(betSelRef);
			});

		});


		// check bet amount on input change
		$('input[name=betvalue]').keyup(function(key) {
			//console.log($(this).val());
			chkTotal();
		});

		function displayError(errMsg) {
			$('.bettixBalances').hide();
			$('.bettixLoader').hide();
			$('.bettixFreeCredits').hide();
			$('.bettixAmntButts').hide();
			$('.bettixDisplay').hide();
			$('.bettixNotice').show();
			$('.bettixNotice').removeClass("noticeMsg").removeClass("successMsg").addClass("errorMsg");
			$('.bettixNoticeText').html("Error: " + errMsg);
			$('.bettixOkButtWrap').show();
		}
		function displayOddsChanged(noticeMsg, betAmount, newOdds) {
			$('.bettixBalances').hide();
			$('.bettixLoader').hide();
			$('.bettixFreeCredits').hide();
			$('.bettixAmntButts').hide();
			$('.bettixDisplay').show();
			$('.bettixNotice').show();
			$('.bettixNotice').removeClass("errorMsg").removeClass("successMsg").addClass("noticeMsg");
			$('.bettixNoticeText').html(noticeMsg);
			$('#betOdds').text(newOdds);
			$('#toWinAmnt').text(number_format((betAmount * newOdds), 2, '.', ','));
		}
		function displaySuccess(successMsg) {
			$('.bettixBalances').hide();
			$('.bettixLoader').hide();
			$('.bettixFreeCredits').hide();
			$('.bettixAmntButts').hide();
			$('.bettixDisplay').hide();
			$('.bettixNotice').show();
			$('.bettixNotice').removeClass("errorMsg").removeClass("noticeMsg").addClass("successMsg");
			$('.bettixNoticeText').html(successMsg);
			$('.bettixOkButtWrap').show();
		}
		function resetBalanceDisp() {
			$('#displayAccountBalance').html('<span class="user-top-amount"><strong>My Balances</strong></span>');
			$('#displayAccountBalance').attr('style', '');
		}
		
		function chkTotal() {
			var currBetTotal = $('#betTicketAmount').val();
			if (currBetTotal <= 0) {
				$('#placeBets').css("opacity", 0.2);
				$('#bettixAmountType').css("opacity", 0);
			} else {
				$('#betAmount').text(currBetTotal);
				$('#toWinAmnt').text(number_format(currBetTotal * $('#betOdds').text(betOdds), 2, '.', ','));
				$('#placeBets').css("opacity", 1);
				$('#bettixAmountType').css("opacity", 1);
			}
		}


		// bet ticket amount buttons
		$('.bettixAmntButt').click(function() {
			var betval = parseInt($(".betTicketAmount").val());
			var buttvalAdd = parseInt($(this).text().slice(1));
			var newButtVal = betval + buttvalAdd;
			if (newButtVal <= TB.fundsTotal2bet) {
				$("#betTicketAmount").val( newButtVal );
				chkTotal();
			} else {
				$("#betTicketAmount").val( TB.fundsTotal2bet );
				//error msg background on
				$('.bettixBalances').css('background', '#f00');
				$('.bettixBalances div').css('color', '#fff');
				//animate error msg background off
				$('.bettixBalances').delay(1500).animate( { backgroundColor: '#f5f5f5' }, 1000);
				$('.bettixBalances div').delay(1500).animate( { color: '#555' }, 900);
			}
		});
		// reset amount
		$('.bettixAmntReset').click(function() {
			//turn off error msg background
			$('.bettixBalances').css('background', '#f5f5f5');
			$('.bettixBalances div').css('color', '#555');

			$("#betTicketAmount").val( 0 );
			chkTotal();
		});
		// ok button
		$('.bettixOkButt').click(function() {
			$('#betTicket').trigger('reveal:close');
			//TODO: do we need to reload the page?
		});


		// initial placing of the bet
		$('#placeBets').click(function() {
			var betAmount = $('#betTicketAmount').val();

			//check the users free account bal / tourn dollars
			//$.get(TBapiUrl + ...
			//if (TB.funds['account_balance'] > betAmount) {
			//var free_acc_bal = 1; //tmp hard set
			if (TB.funds['tournament_dollars'] > 0) {
				chkboxYes();
				$('.bettixFreeCredits').show();
				$('.bettixCreditTextOff').hide();
				$('.bettixCreditTextOn').show();
				TB.FCchecked = 1;
				
				if (TB.funds['tournament_dollars'] > betAmount) {
					var FCused = betAmount;
				} else {
					var FCused = TB.funds['tournament_dollars'];
				}
				$('#FCval').text(FCused);
			} else {
				chkboxNo();
				$('.bettixFreeCredits').hide();
				TB.FCchecked = 0;
			}
			
			//setup the free credit vars
			$('#betTicketAmount').val()
			//display the free credit checkbox

			//show the confirm ticket
			$('.bettixAmntButts').hide();
			$('#placeBets').hide();
			$('#confirmBets').css('display', 'inline-block');
			//$("#sportsBetForm").submit();

		});

		// confirm and send the bet thru API
		$('#confirmBets').click(function() {
			var eventId = ""; // needs to be set to tbdb_event >> event_id
			var betTypeRef = $('#betTypeRef').val();			
			var betAmount = $('#betTicketAmount').val()*100; //convert to cents
			var betOdds = $('#betOdds').text();
			var extSelID = $('#extSelID').val();
			var betPlaceRef = $('#betRef').val();			
			var eventId = $('.eventButtonActive').attr('id').slice(4);

			//setup the bet_data object
			var bet_data = {
				event_id: extSelID,
				bet_type_id: betTypeRef, 
				value: betAmount, 
				dividend: betOdds,
				//dividend: "201", //tmp hard set to test odds changed
				selection: betPlaceRef, 
				bet_origin: "sports", 
				bet_product: "", // not set atm 
				//wager_id: betPlaceRef, 
				chkFreeBet: TB.FCchecked
			};
			console.log(bet_data);
			
			//place the bet
			saveSportsBet(bet_data);
			
			//store the bet amount
			localStorage.betAmount = betAmount/100; //in dollars
			
			//adjust the ticket display
			$('.bettixNotice').hide();
			$('.bettixDisplay').hide();
			$('.bettixBalances').hide();
			$('.bettixFreeCredits').hide();
			$('.bettixAmntButts').hide();
			$('.bettixLoader').show();
			
			//$("#sportsBetForm").submit();
		});

		//save the bet through the TB api
		function saveSportsBet(bet_data) {
			var TBapiUrl = TB.apiUrl;
			$.get(TBapiUrl + 'method=getLoginHash', function(obj) {
				var login_hash = obj.login_hash;
				 //console.log('login hash:' + login_hash);
				bet_data[login_hash] = 1;
				 //console.log(bet_data);
		
			    $.ajax({
			        type: "POST",
			        url: TBapiUrl + 'method=saveSportsBet',
			        data: bet_data,
			        dataType: "json",
			        timeout: 20000, // in milliseconds
			        success: function(obj) {
			            // process data here
						if (obj.status === 200) {
							//console.log("obj: " + JSON.stringify(obj));
							console.log($('#betOdds').text());
							
							//TODO: add in the odds changed bet HAS been placed check & message
							//obj.actualDividends
							if (obj.success) {
								if (obj.actualDividends != $('#betOdds').text()) {
									var betReturn = obj.success + ". Odds changed to " + obj.actualDividends;
								} else {
									var betReturn = obj.success;
								}
							} else {
								//alert('Bet placed');
								var betReturn = 'Bet has been placed';
							}
							displaySuccess(betReturn);
							resetBalanceDisp();
							//console.log(localStorage.betAmount);
							
						} else if (obj.status === 400) {
							console.log(obj);
						
							//400 = odds changed, bet NOT placed
							var newOdds = obj.new_odds;
							var errorMsg = "The odds for that selection have changed.<br />Would you like to place the bet at the new odds below?";
							var betAmount = $('#betTicketAmount').val();
							console.log("amnt: " +betAmount+" - odds: " +newOdds);
							displayOddsChanged(errorMsg, betAmount, newOdds);
							resetBalanceDisp();
						} else {
							//there was an error
							displayError(obj.error_msg);
						}

						//close the modal
						//$('#betTicket').trigger('reveal:close');
			        },
			        error: function(request, status, err) {
			            //console.log("error" + request, status, err);
						displayError('There was a problem placing your bet.');
			        }
			    });
    
			}, "json");
			//console.log(betReturn);
		}			


	//toggle the free credit check box
	$('.bettixCreditCheckBox').click(function() {		
		if ($('.bettixCreditCheckBox').css('float') == 'right') { // yes
			//change to no

			//chk if they have enough account blance
			var betAmount = $('#betTicketAmount').val();
			//TB.funds['tournament_dollars'] //account_balance

			if (TB.funds['account_balance'] >= betAmount) {
				//change to no
				chkboxNo();
			} else {
				return;
			}
		} else { // no
			//change to yes
			chkboxYes();
		}
	});

	function chkboxNo() {
		$('.bettixCreditCheckBox').css('float', 'left');
		$('.bettixCreditCheckLabel').css('float', 'right');
		$('.bettixCreditCheckLabel').css('color', '#7F7F7F');
		$('.bettixCreditCheck').css('background', '#dadada');
		$('.bettixCreditTextOn').hide();
		$('.bettixCreditTextOff').show();
		$('.bettixCreditCheckLabel').text('NO');
		TB.FCchecked = 0;
	}
	function chkboxYes() {
		$('.bettixCreditCheckBox').css('float', 'right');
		$('.bettixCreditCheckLabel').css('float', 'left');
		$('.bettixCreditCheckLabel').css('color', '#ffffff');
		$('.bettixCreditCheck').css('background', '#36a8f9');
		$('.bettixCreditTextOff').hide();
		$('.bettixCreditTextOn').show();
		$('.bettixCreditCheckLabel').text('YES');
		TB.FCchecked = 1;
	}

     
/*
$('#username').focus(function() {
  this.value="";
});
*/ 



	});
} ) ( jQuery );


// mootools functions
window.addEvent('domready', function() {
	var sportsAccordion = new Accordion('div.accordToggler', 'div.accordElement', {
		show: -1,
		opacity: true,
		alwaysHide: true,
		onActive: function(toggler, element){
			element.setStyle('display', 'block');
			element.setStyle('margin-bottom', '10px');
			//toggler.getElement('.toggArr').setStyle('background-position-y', '-186px');
			$E('.toggArr', toggler).setStyle('background-position-y', '-186px');
		},
		onBackground: function(toggler, element){
			toggler.getElement('.toggArr').setStyle('background-position-y', '-216px');
			element.setStyle('margin-bottom', '0');
		}
	});

	var sActive = $E('div.sElActive');
	sportsAccordion.display(sActive);

});


//other functions

