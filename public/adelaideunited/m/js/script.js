jQuery(function($){
	//COUNTDOWN TIMER
	var austDay = new Date();
	//austDay = new Date(austDay.getFullYear() + 1, 1 - 1, 26);
	austDay = new Date("February 10, 2013 15:00");
	$('#timer').countdown({
		until: austDay,
		timezone: +11,
		//layout:'<b>{d<}{dn} {dl} and {d>}'+ '{hn} {hl}, {mn} {ml}, {sn} {sl}</b>',
		layout:'<div class="countdown_section"><span class="countdown_amount">{dn}</span><span class="label">{dl}</span></div> <div class="countdown_section"><span class="countdown_amount">{hn}</span><span class="label">{hl}</span></div> <div class="countdown_section"><span class="countdown_amount">{mn}</span><span class="label">{ml}</span></div> <div class="countdown_section"><span class="countdown_amount">{sn}</span><span class="label">{sl}</span></div>',
	});
	$('#year').text(austDay.getFullYear());
	
});




