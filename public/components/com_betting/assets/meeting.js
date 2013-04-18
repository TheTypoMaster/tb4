<!--

var MeetingController = new Class({
	Implements: [Options, Events],
		options: {
		id:				null,
		race_id:		null,
		race_number:	null,
		race_status:	null,
		time:			0,
		bet_type_list: 	[],
		bet_type: 		null,
		base_url: 		null
	},
	initialize: function(options) {
		if(options) {
			for(var i in options) {
				this.options[i] = options[i];
			}
		}
	},
	placeBet: function() {
		//pre js validations
		var selection = this.getBetSelection();
		
		if(selection['count'] == 0) {
			alert('You need to make your selection(s)');
			this.highlightSelectionUI();
			return false;
		}

		var value = parseFloat(this.getBetValue());
		if(value <= 0) {
			alert('You need to specify a bet value');
			this.setBetValue('');
			return false;
		}

		if(!value) {
			alert('Invalid bet value specified');
			this.setBetValue('');
			return false;
		}
		
		var bet_origin = this.getBetOrigin();
		
		var bet_type = this.options.bet_type_list[this.options.bet_type];
		var base_url = this.options.base_url;

		base_url	+= '&id=' + this.options.id;
		base_url	+= '&race_id=' + this.options.race_id;
		base_url	+= '&bet_type_id=' + this.options.bet_type;
		base_url	+= '&value=' + value;
		base_url	+= '&bet_origin=' + bet_origin;
		
		selections = new Array();
		switch (bet_type) {
			case 'win':
			case 'place':
			case 'eachway':
			case 'quinella':
				selections = this.getBetSelection('.firstP');
				break;
			case 'exacta':
				selections = this.getBetSelection('.secondP');
				break;
			case 'trifecta':
				selections = this.getBetSelection('.thirdP');
				break;
			case 'firstfour':
				selections = this.getBetSelection('.fourthP');
				break;
		
		}
		
		for (var i=0; i< selection['fields'].length; i++) {
			var field_name = selection['fields'][i];
			for (var j=0; j < selection[field_name].length; j++) {
				base_url += '&' + field_name + '=' + selection[field_name][j];
			}
		}
		
		validation_url  = base_url + '&task=ajaxvalidatebet';
		validation_url += '&format=raw';
		
		var jsonRequest = new Json.Remote(validation_url, {
			onComplete: function(callback) {
				if (callback.error) {
					alert(callback.error);
					if (callback.relogin) {
						window.location.reload();
					}
				} else {
					bet_url = base_url + '&task=confirmbet';
					params = {
						'width': 545,
						'height' :375,
						'load_method': 'post',
						'load_data': {
							'bet_type_name'	: callback.bet_type_name,
							'data'			: callback.data
						},
						'on_complete' : 
							"$('confirmBets').addEvent('click', function(e){" +
							"this.disabled = true;" +
							"new Event(e).stop();" +
							"$('atpBetForm').send({" +
							"onRequest: function() {" +
							"$('button_group').setHTML('<div id=\"processingBets\"><img src=\"/templates/topbetta/images/loading-dark.gif\" />Processing Bet</div>');" +
							"}," +
							"onComplete: function() {" +
								"window.location.reload();" +
							"}" +
							"});" +
							"});" 
					}
					loadLightbox(bet_url, params);
				}
			}
		}).send();
		
		return false;
	},
	getBetSelection: function(classname) {
		var selection = new Array();
		var i = 0;
		if(!classname) {
			classname = '.selBoxes input';
		} else {
			classname = '.selBoxes ' + classname + ' input';
		}
		
		selection['count']	= 0;
		selection['fields']	= [];
		
		$$(classname).each(function(el) {
			if (el.checked) {
				if (!selection[el.getProperty('name')]) {
					selection[el.getProperty('name')] = [];
					selection['fields'].push(el.getProperty('name'));
				}
				selection[el.getProperty('name')].push(el.getProperty('value'));

				i++;
			}
		});
		selection['count'] = i;

		return selection;
    },
    getBetValue: function() {
    	return $('betValueB').value;
    },
    getBetOrigin: function() {
    	return $('betOrigin').value;
    },
    setBetValue: function(value) {
    	$('betValueB').value = value;
    },
    highlightBetUI: function() {
    	$("betValueB").setStyle('border-color', 'red');
    	$("betValueB").focus();
    },
    highlightSelectionUI: function() {
    	this.updateSelectionUI('hightlight');
    },
    selectField: function(e) {
      var state = e.checked;
      var a_class;
      
      if (e.hasClass('firstP')) {
          a_class = '.firstA';
      } else if (e.hasClass('secondP')) {
    	  a_class = '.secondA';
      } else if (e.hasClass('thirdP')) {
    	  a_class = '.thirdA';
      } else {
    	  a_class = '.fourthA';
      }
      
      $$(a_class).each(function(el) {
        if(!el.disabled) {
          el.checked = state;
        }
      });
    },
    getButtonID: function(id) {
      return this.options.bet_type_list[id].split(' ').join('') + 'ButtID';
    },
    setButtonStyle: function(id) {
      if(!id) {
        id = this.options.bet_type;
      }

      var button_id = this.getButtonID(id);
      if(button_id) {
    	  $(button_id).setStyle('background-position', '0px -66px');
      }
    },
    clearButtonStyle: function(id) {
      var button_id = this.getButtonID(id);
      if($(button_id)) {
	      $(button_id).removeClass('typeButtsG');
	      $(button_id).addClass('typeButts');
	      $(button_id).setStyle('background-position', '0px -40px');
      }
    },
    clearAllButtonStyle: function() {
      for(var index in this.options.bet_type_list) {
        this.clearButtonStyle(index);
      }
    },
    updateCheckBoxes: function(id) {
    	if(!id) {
    		bet_type = this.options.bet_type_list[this.options.bet_type];
    	} else {
    		bet_type = this.options.bet_type_list[id];
    	}
    	
    	$$('.selBoxes').each(function(el) {
    		el.setStyle('color', 'gray');
    	});
    	
    	this.disableCheckBoxes('.chkbox');
    	
    	this.updateSelectionUI('switch');
    	
    },
    disableCheckBoxes: function(chkbox_class) {
    	
    	var bet_type	= this.options.bet_type_list[this.options.bet_type];
    	var check_class = 'firstP';
    	switch (bet_type) {
			case 'exacta':
				check_class = 'secondP';
			break;
			case 'trifecta':
				check_class = 'thirdP';
			break;
			case 'firstfour':
				check_class = 'fourthP';
			break;
    	}
    	
    	$$(chkbox_class).each(function(el) {
    		el.setProperty('disabled', 'disabled');
    		if(!el.hasClass(check_class)) {
        		el.setProperty('checked', '');
    		}
    	});
    },
    refresh: function(id) {
		if (!$('refreshButtID').hasClass('raceTable-refreshing')) {
			$('refreshButtID').addClass('raceTable-refreshing');
			var second = 5;
			$('bet-refresh-countdown').setText('(' + second + ')');
			
			this.updateRunnerList(id);
			
			var countDown = function() {
				second--;
				$('bet-refresh-countdown').setText('(' + second + ')');
				if (second < 0) {
					$clear(refreshCountDown);
					
					$('refreshButtID').removeClass('raceTable-refreshing');
					$('bet-refresh-countdown').setText('');
				}
			}
			var refreshCountDown = countDown.periodical(1000);
		}
    },
    updateRunnerList: function(id) {
    	var url = '/index.php?option=com_betting&task=meeting&meeting_id='+ this.options.id +'&number='+ this.options.race_number + '&layout=runnerlist&format=raw';
    	
    	var controller = this;
    	new Ajax(url, {
    		method: 'get',
    		onRequest: function() { },
    		update: $('raceTable'),
    		evalScripts: true,
    		onComplete: function() {
    			controller.updateCheckBoxes(id);
				$$('.selectA').addEvent('click', function(e) {
					controller.selectField(this);
				});
    		}
    	}).request();
    	
    },
    updateSelectionUI: function(type) {
    	var bet_type = this.options.bet_type_list[this.options.bet_type];

    	var label_color = '#FFF';
    	
    	if ('hightlight' == type) {
    		label_color = '#E97707';
    	}
    	switch (bet_type) {
			case 'win':
			case 'place':
			case 'eachway':
			case 'quinella':
				if ('switch' == type) {
			    	$$('.firstP').each(function(el) {
			    		el.removeProperty('disabled');
			    	});
				}
		    	$$('.sb1').each(function(el) {
		    		el.setStyle('color', label_color );
		    	});
			break;
			case 'exacta':
				if ('switch' == type) {
			    	$$('.secondP').each(function(el) {
			    		el.removeProperty('disabled');
			    	});
				}
		    	$$('.sb2').each(function(el) {
		    		el.setStyle('color', label_color );
		    	});
			break;
			case 'trifecta':
				if ('switch' == type) {
			    	$$('.thirdP').each(function(el) {
			    		el.removeProperty('disabled');
			    	});
			    	//$('flexi').setStyle('color', '#000');
				}
		    	$$('.sb3').each(function(el) {
		    		el.setStyle('color', label_color );
		    	});
			break;
			case 'firstfour':
				if ('switch' == type) {
			    	$$('.fourthP').each(function(el) {
			    		el.removeProperty('disabled');
			    	});
			    	//$('flexi').setStyle('color', '#000');
				}
		    	$$('.sb4').each(function(el) {
		    		el.setStyle('color', label_color );
		    	});
			break;
    	}
    	
    	$$('.scratched td input').each(function(el) {
    		el.setProperty('disabled', 'disabled');
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

      var hours 	= parseInt(time / 3600);
      var minutes	= parseInt((time / 60) % 60);
      var seconds 	= parseInt(time % 60);

      var text = seconds + ' sec';
      if(minutes > 0) {
        text = minutes + ' min';
      }

      if(hours > 0) {
        text = hours + ' hr ' + text;
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
//      if ($('betBoxG')) { 
//          $('betBoxG').remove();
//      }
//      
//      if ($('btButts')) {
//    	  $('btButts').remove();
//      }
//      
//      $$('#bucksbar .tournDetails').addClass('wideHdr');
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