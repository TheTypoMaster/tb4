<!--
/*
	Common JS methods to be used site wide
	This has the fix with IE & mootools 1.1
	of adding options dynamically in a <select>
*/

function loadOptions(url, target, default_option, complete_trigger){
	var url = url + "&format=raw";

	var default_option = (typeof(default_option) == 'undefined' ? 'Select...' : default_option);
	var complete_trigger = (typeof(complete_trigger) == 'undefined' ? '' : complete_trigger);

	new Ajax(url, {
		method: 'get',
		onRequest: function() { },
		onComplete: function(response) {
			$(target).empty();
			if(default_option){
				optionElement = new Element('option');
				optionElement.inject($(target));
				optionElement.setProperty('value', '');
				optionElement.appendText(default_option);
			}

			if(''!=response.trim()){
				var arrOptions = response.split('_|_');
				for(i=0; i < arrOptions.length; i++ ){
					var selOpt= new Array();
					selOpt = arrOptions[i].split('_:_');
					key = selOpt[0].trim();
					labelOpt = selOpt[1].split('!selected');
					label = labelOpt[0];
					selected = (typeof(labelOpt[1]) != 'undefined') ? true : false;


					optionElement = new Element('option');
					optionElement.inject($(target));
					optionElement.setProperty('value', key);
					if(selected) {
						optionElement.setProperty('selected', 'selected');
					}
					optionElement.appendText(label);
				}
			}
			if(complete_trigger) {
				eval(complete_trigger);
			}
		}
	}).request();
}


/**
 * LoadLightbox loads the content through Ajax from the given url
 */
function loadLightbox(url, params){
	var width = params.width;
	var height = params.height;
	
	var dynamic_size = params.dynamic_size;
	var div_to_reset = params.div_to_reset;
	var load_method = params.load_method;
	var load_data = params.load_data;
	var load_handler = params.load_handler;
	var on_complete = params.on_complete;
	
	if(dynamic_size == 'undefined') {
		dynamic_size = false;
	}

	if(div_to_reset == 'undefined') {
		div_to_reset = null;
	}
	
	if(load_method == 'undefined') {
		load_method = 'get';
	}
	
	if(load_data == 'undefined') {
		load_data = {};
	}
	
	if(load_handler == 'undefined') {
		load_handler = 'url';
	}
	
	if(on_complete == 'undefined') {
		on_complete = null;
	}

	if(SqueezeBox.isOpen) {
		SqueezeBox.close();
	}

    if(dynamic_size) {
	    SqueezeBox.presets = $merge(
	    		SqueezeBox.presets,
	    		{
		    		ajaxOptions: {
	    				method: load_method,
    					data: load_data,
		    			evalScripts: true,
		    			onComplete: function(response) {
		    					need_resize = false;
		    					if($('sbox-content').scrollHeight > height) {
		    						height = $('sbox-content').scrollHeight;
		        					need_resize = true;
		    					}
		    					if($('sbox-content').scrollWidth > width) {
		    						width = $('sbox-content').scrollWidth;
		        					need_resize = true;
		    					}
		    					if(need_resize) {
		    						
		    						SqueezeBox.resize({x: width, y: height}, false);

		    						if(div_to_reset && $(div_to_reset)) {
		    							$(div_to_reset).setStyle('height', height);
		    							$(div_to_reset).setStyle('width', width);
		    						}
		    					}
		    					SqueezeBox.overlay.setStyle('display', '');
		    					eval(on_complete);
		    			}
		    		}
	    		}
	    );
		SqueezeBox.setOptions(SqueezeBox.presets);
		SqueezeBox.content.setStyle('overflow', 'hidden');
    } else {
	    SqueezeBox.presets = $merge(
	    		SqueezeBox.presets,
	    		{
		    		ajaxOptions: {
    					method: load_method,
    					data: load_data,
		    			evalScripts: true,
		    			onComplete: function(response) {
	    					eval(on_complete);
		    			}
		    		}
	    		}
	    );
    	
    }
	
    SqueezeBox.fromElement(url, {handler: load_handler, size: {x: width, y: height}});
}
-->