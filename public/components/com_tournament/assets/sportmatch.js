<!--
  var TournamentController = new Class({
    Implements: [Options, Events],
    options: {
      id: 				null,
      match_id: 		null,
      time: 			0,
      bet_url: 			''
    },
    initialize: function(options) {
      if(options) {
        for(var i in options) {
          this.options[i] = options[i];
        }
      }
    },
    updateTotal: function() {
    	$$('.sports-bet-input').each(function(el) {
    	    el.addEvent('keyup', function(e) {
    	    	bet_value	= parseFloat(el.value);
    	    	id			= el.getProperty('id');
		    	market_item_id = this.getProperty('ref');
		    	
    	    	if(bet_value) {
    		    	odds		= $(id+'_odds').innerHTML;
    		    	win			= bet_value * odds;

    		    	$(id+'_win').innerHTML = win.toFixed(2);
    		    	
    	    	} else {
    	    		$(id+'_win').innerHTML = '-';
    	    	}
    	    	
		    	market_bet_value = 0;
		    	$$('.sports-bet-input').each(function(el) {
		    		bet_input_value = parseFloat(el.value);
		    		
		    		if(bet_input_value) {
		    			market_bet_value += bet_input_value;
		    		}
		    	});

		    	if(market_bet_value == 0) {
		    		$(market_item_id).innerHTML = '-';
		    	} else {
		    		$(market_item_id).innerHTML = market_bet_value.toFixed(2);
		    	}
    	    })
    	});
    },
    changeMarket: function() {
    	$$('.marketItems').each(function(el) {
    		el.addEvent('click', function(e) {
    			new Event(e).stop();
    			var market_id = el.getProperty('ref');
    		    var value = 0;
    		    var has_error = false;
    		    $$('.sports-bet-input').each(function(el) {
    		  	  if('' != el.value) {
    		      	  bet_value = parseFloat(el.value);
    		      	  
    		      	  if(!bet_value) {
    		      		  el.setStyle('border-color', 'red');  
    		      		  has_error = true;
    		      	  } else {
    		      		  bet_value = parseFloat(el.value);
    		      		  el.setStyle('border-color', '#568501');
    		      		  value = value + bet_value;
    		      	  }
    		  	  }
    		    });
    		    
    		    if(has_error) {
    				  alert('Invalid bet value specified');
    				  return false;
    		    }
    	    	$$('.bet-types-table td').each(function(el) {
    	    		el.removeClass('selected');
    	    	});
    	    	market_id = el.getProperty('ref');
    	    	$('market_item_' + market_id).addClass('selected');
    		    
    		    $('market_id').setProperty('value', market_id);
    		    $('offerForm').send({
    		    		evalScripts: true,
    		    		update: $('offer_list'),
    		    		onComplete: function() {
    		    			$('refresh_odds').setProperty('ref', market_id);
    		    		}
    		    	}
    		    );
    	  	});
    	});
    },
    updateOfferList: function() {
    	$('refresh_odds').addEvent('click', function(e) {
        	market_id = $('refresh_odds').getProperty('ref');
    	    $('offerForm').send({
	    		evalScripts: true,
	    		update: $('offer_list'),
	    		onComplete: function() {
	    			$('refresh_odds').setProperty('ref', market_id);
	    		}
    	    });
    	});
    },
    placeBet: function() {
      if(this.options.time <= 0) {
        alert('Match has already started');
        return false;
      }

      var selection = this.getBetSelection();
      if(selection.length == 0) {
        alert('You need to enter your bet amount');
        return false;
      }

      var bet_url = this.options.bet_url;
      bet_url += '&id=' + this.options.id;
      bet_url += '&match_id=' + this.options.match_id;
      bet_url += '&market_id=' + $('from_market_id').value;
      
      var value = 0;
      var has_error = false;
      $$('.sports-bet-input').each(function(el) {
    	  if('' != el.value) {
        	  bet_value = parseFloat(el.value);
        	  
        	  if(!bet_value) {
        		  el.setStyle('border-color', 'red');  
        		  has_error = true;
        	  } else {
        		  bet_value = parseFloat(el.value);
        		  el.setStyle('border-color', '#568501');
        		  bet_url += '&' + el.getProperty('name') + '=' + bet_value;
        	  }
    	  }
      });
      if(has_error) {
		  alert('Invalid bet value specified');
		  return false;
      }
      
      params = {width:545, height:375};
      loadLightbox(bet_url, params);
      return false;
    },
    getBetSelection: function() {
      var selection = [];
      $$('.sports-bet-input').each(function(el) {
        if(el.value) {
          selection.push(el.value);
        }
      });
      return selection;
    },
    getBetValue: function() {
      return $('betValueG').value;
    },
    setBetValue: function(value) {
      $('betValueG').value = value;
    },
    selectField: function(state) {
      $$('.A').each(function(el) {
        if(!el.disabled) {
          el.checked = state;
        }
      });
    }
  });

  var CounterController = new Class({
    Implements: [Options, Events],
    updateUI: function(time) {
      if(time <= 0) {
        $('jumpLabel').innerText = '';
      }

      if(time < 0) {
        return
      }

      if(time < 60) {
        this.flashCounter();
      }

      var text 		= this.formatCounter(time);
      var timeout 	= 1000;

      $('cntdwnVal').innerHTML = text;
    },
    formatCounter: function(time) {
      if(time <= 0) {
        return 'PAST START TIME';
      }

      var days		= parseInt(time / 3600 / 24);
      var hours 	= parseInt((time / 3600) % 24);
      var minutes	= parseInt((time / 60) % 60);
      var seconds 	= parseInt(time % 60);

      var text = seconds + ' sec';
      if(minutes > 0) {
        text = minutes + ' min';
      }

      if(hours > 0) {
    	min_sec_text = '';
    	if(days == 0) {
    		min_sec_text = text;
    	}
        text = hours + ' hr ' + min_sec_text;
      }
      
      if(days > 0) {
    	text = days + ' d ' + text;  
      }

      return text;
    },
    flashCounter: function() {
      $('cntdwnVal').setStyle('visibility', 'hidden');
      setTimeout("$('cntdwnVal').setStyle('visibility', 'visible')", 300);
    }
  });

  var BetFormController = new Class({
    Implements: [Options, Events],
    updateUI: function(time) {
      if(time > 0) {
        return;
      }
      this.removeBetForm();
    },
    removeBetForm: function() {
      $('confirmBetsG').remove();
      $$('.sports-bet-input').addProperty('disabled', 'disabled');
    }
  });

  var TimerController = new Class({
    Implements: [Options, Events],
    options: {
      time:		0,
      timeout: 	0
    },
    controllers: [],
    periodicalID: null,
    initialize: function(options) {
      this.options = options;
    },
    addController: function(controller) {
      this.controllers.push(controller);
    },
    start: function() {
      this.periodicalID = this.countDown.periodical(this.options.timeout, this);
    },
    stop: function() {
      $clear(this.periodicalID);
    },
    countDown: function() {
      this.options.time -= this.options.timeout / 1000;
      for(var i in this.controllers) {
        if(typeof this.controllers[i] == 'object') {
          this.controllers[i].updateUI(this.options.time);
        }
      }

      if(this.options.time < 0) {
        this.stop();
      }
    }
  });
-->