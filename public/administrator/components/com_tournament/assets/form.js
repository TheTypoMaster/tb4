<!--
	var AjaxSelectInput = new Class({
		options: {
			select_id:			null,
			callback: 			null,
			request_base: 		null,
			trigger_id: 		null,
			complete_trigger:	null
		},
		initialize: function(option_list) {
			if(option_list) {
				for(var i in this.options) {
					this[i] = (option_list[i]) ? option_list[i] : this.options[i];
				}
			}	
			this.bindEvent();			
		},
		bindEvent: function() {

			$(this.trigger_id).addEvent('change', function(event) {
				
				target = event.target || event.srcElement;

				this.ajaxCallBack(target.value);
			}.bind(this));
		},
		ajaxCallBack: function(value) {
			
			var ajax = new Ajax(this.request_base, {
				method: 	'post',
				onSuccess: 	this.updateOptionList.bind(this)
			}).request({
				'task': 	this.callback,
				'value': 	value
			});
			
		},

		updateOptionList: function(text, xml) {
			
			$(this.select_id).empty();
			
			text = eval(text);

			option_count = text.length;
			
			text.each(function(e, i) {
				optionElement = new Element('option');
				optionElement.inject($(this.select_id));
				optionElement.setProperty('value', e.value);
				optionElement.appendText(e.title);
				
				if(option_count==2 && i==1) {
					optionElement.setProperty('selected', 'selected');
					
					if(this.complete_trigger) {
						trigger_ajax = new AjaxSelectInput(this.complete_trigger);
						trigger_ajax.ajaxCallBack(e.value);
					}
				}
			}.bind(this));
			

		}
	});
//-->