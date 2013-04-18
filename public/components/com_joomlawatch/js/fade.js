// ***************************************************************************
// fade.js
// by i am jack's design (http://www.iamjacksdesign.com)
// last modified: 2/20/05
// ***************************************************************************

// FADE.JS WORKS OFF OF RGB VALUES FOR IT'S BASE COLOR. 
// SET THE THREE VARIABLES BELOW TO THE RED, GREEN AND BLUE VALUES FOR YOUR 
// DESIRED BASE COLOR.

var FADE_RED = 255;
var FADE_GREEN = 253;
var FADE_BLUE = 55;

// THE THREE VARIABLES BELOW DEFINE THE MOVEMENT OF THE FADE:
//
// 		FADE_HOLD 	= Time (in milliseconds) that your base color lasts
//					  before the fade begins.
//		FADE_SPEED 	= Time (in milliseconds) that each color of the fade lasts
//		FADE_STEP	= Increase in the RGB value per color change

var FADE_HOLD = 500;
var FADE_SPEED = 100;
var FADE_STEP = 25;




// FOR BASIC FUNCTIONALITY, LEAVE EVERYTHING BELOW THIS POINT AS IS.
var fade_r = FADE_RED
var fade_g = FADE_GREEN
var fade_b = FADE_BLUE

function fade(container)
{
try {
	if (fade_r == 0) fade_r == FADE_RED;
	if (fade_g == 0) fade_g == FADE_GREEN;
	if (fade_b == 0) fade_b == FADE_BLUE;
	
	if (fade_r + fade_g + fade_b != (255 * 3))
	{	
		document.getElementById(container).style.background = "rgb(" + fade_r + "," + fade_g + "," + fade_b + ")";
		
		if ((fade_r == FADE_RED) && (fade_g == FADE_GREEN) && (fade_b == FADE_BLUE))
		{
			setTimeout('fade("' + container + '")', FADE_HOLD)
		}
		else
		{	
			setTimeout('fade("' + container + '")', FADE_SPEED)
		}
		
		if ((fade_r >= 255) || (fade_r + FADE_STEP > 255)) fade_r = 255; else fade_r = fade_r + FADE_STEP;
		if ((fade_g >= 255) || (fade_g + FADE_STEP > 255)) fade_g = 255; else fade_g = fade_g + FADE_STEP;
		if ((fade_b >= 255) || (fade_b + FADE_STEP > 255)) fade_b  = 255; else fade_b = fade_b + FADE_STEP;
	}
	else
	{	
		document.getElementById(container).style.background = "rgb(" + fade_r + "," + fade_g + "," + fade_b + ")";
		fade_r = FADE_RED;
		fade_g = FADE_GREEN;
		fade_b = FADE_BLUE;
	}
	}
	catch (err) {
	
	}
}