<!--
	var TournamentController = new Class({
		Implements: [Options, Events],
		options: {
			id: 			null,
			race_id: 		null,
			time: 			0,
			bet_type_list: 	[],
			bet_type: 		null,
			bet_url: 		''
		},
		initialize: function(options) {
			if(options) {
				for(var i in options) {
					this.options[i] = options[i];
				}
			}
		},
		placeBet: function() {
			bet_type_name = this.options.bet_type_list[this.options.bet_type];
			if (bet_type_name != 'win' && bet_type_name != 'place' && bet_type_name != 'eachway' ) {
				alert('Exotic bets are not currently supported for tournaments. Coming soon!');
				return false;
			}
			
			if (this.options.time <= 0) {
				alert('Race has already jumped');
				return false;
			}

			var selection = this.getBetSelection();
			if (selection.length == 0) {
				alert('You need to make your selection(s)');
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

			var bet_url = this.options.bet_url;

			bet_url += '&id=' + this.options.id;
			bet_url += '&race_id=' + this.options.race_id;

			bet_url += '&bet_type_id=' + this.options.bet_type;
			bet_url += '&value=' + value;

			bet_url += '&selection=' + selection.join(',');

			params = {
					'width': 545,
					'height': 375,
					'on_complete' : 
						"$('confirmBets').addEvent('click', function(e) {" +
						"$('button_group').setStyle('display', 'none');" +
						"$('processingBets').setStyle('display', '');" +
						"});"
						
			}
			loadLightbox(bet_url, params);
			return false;
		},
		
		getBetSelection: function() {
			var selection = [];
			$$('.firstA').each(function(el) {
				if(el.checked && el.id != 'selectA') {
					selection.push(el.value);
				}
			});
			return selection;
		},
		
		getBetValue: function() {
			return $('TournBetValueG').value;
		},
    
		setBetValue: function(value) {
			$('TournBetValueG').value = value;
		},
		highlightBetUI: function() {
			$("TournBetValueG").setStyle('border-color', 'red');
			$("TournBetValueG").focus();
		},
		highlightSelectionUI: function() {
			$$(".sb1").each(function(el){
				el.setStyle('background', 'red');
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
		}
  });

-->