jQuery(function($){
	//COUNTDOWN TIMER
	//date set with >> new Date(year, month, day, hours, minutes, seconds, milliseconds)
	//     seconds, milliseconds etc can be left off the end or set to 0

	//note: the use of 1-1 for the month is just to make the month more humanly readable
	//      the month range jan to dec = 0 to 11 ... so 1-1=0=jan  as Javascript months count from zero instead of 1
	//countDownDate = new Date(2013, 2-1, 03, 16, 30, 0, 0);
	countDownDate = new Date("March 28, 2013 19:30");
	$('#timer').countdown({
		until: countDownDate,
		//set the timezone of the until date
		timezone: +11,
		//layout:'<b>{d<}{dn} {dl} and {d>}'+ '{hn} {hl}, {mn} {ml}, {sn} {sl}</b>',
		layout:'<div class="countdown_section"><span class="countdown_amount">{dn}</span><span class="label">{dl}</span></div> <div class="countdown_section"><span class="countdown_amount">{hn}</span><span class="label">{hl}</span></div> <div class="countdown_section"><span class="countdown_amount">{mn}</span><span class="label">{ml}</span></div> <div class="countdown_section"><span class="countdown_amount">{sn}</span><span class="label">{sl}</span></div>',
	});
	
});
