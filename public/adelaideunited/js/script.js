jQuery(function($){

	//TWITTER
	$.ajax({
		url: 'http://api.twitter.com/1/users/show.json',
		data: { screen_name: 'TopBetta' },
		dataType: 'jsonp',
		success: function(data) {
		$('#followers').html(data.followers_count.
		toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ","));
		}	});
	$(".tweet").tweet({
		username: "TOPBETTA",
		join_text: "auto",
		avatar_size: 16,
		count: 2,
		//auto_join_text_default: " we said,", 
		//auto_join_text_ed: "we",
		//auto_join_text_ing: "we were",
		//auto_join_text_reply: "we replied to",
		//auto_join_text_url: "we were checking out",
		auto_join_text_default: "", 
		auto_join_text_ed: "",
		auto_join_text_ing: "",
		auto_join_text_reply: "",
		auto_join_text_url: "",
		loading_text: "loading tweets..."
	});
	
	$('a[rel*=facebox]').facebox();
	
	
});

function printObject(o) {
  var out = '';
  for (var p in o) {
    out += p + ': ' + o[p] + '\n';
  }
  alert(out);
  //$("#signup_result").html(out);
}

function getFirstElement(data) {
  for (var prop in data)
    return prop;
}

$(function(){
	$('#winners-list').vTicker({ 
		speed: 500,
		pause: 3000,
		animation: 'fade',
		mousePause: false,
		showItems: 3
	});
});

