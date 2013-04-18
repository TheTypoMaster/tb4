<?php

/**
* JoomlaWatch - A real-time ajax joomla monitor and live stats
* @version 1.2.x
* @package JoomlaWatch
* @license http://www.gnu.org/licenses/gpl-3.0.txt 	GNU General Public License v3
* @copyright (C) 2007 by Matej Koval - All rights reserved! 
* @website http://www.codegravity.com
**/

/** ensure this file is being included by a parent file */
if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' );

/* This is the main file with basic settings */


/* LIVE SITE:
* (remove //) of the following line to override the live site setting:
*/

// define('JOOMLAWATCH_LIVE_SITE','http://www.yoursite.com/');


define('JOOMLAWATCH_VERSION', "1.2.9");

define('JOOMLAWATCH_DEBUG', 0);

define('JOOMLAWATCH_STATS_MAX_ROWS', '20');
define('TYPE_JOOMLAWATCH_STATS_MAX_ROWS', "number");

define('JOOMLAWATCH_STATS_IP_HITS', '20');
define('TYPE_JOOMLAWATCH_STATS_IP_HITS', "number");

define('JOOMLAWATCH_STATS_URL_HITS', '20');
define('TYPE_JOOMLAWATCH_STATS_URL_HITS', "number");

define('JOOMLAWATCH_IGNORE_IP', '   ');
define('TYPE_JOOMLAWATCH_IGNORE_IP', "text");

define('JOOMLAWATCH_IGNORE_USER', '   ');
define('TYPE_JOOMLAWATCH_IGNORE_USER', "text");

define('JOOMLAWATCH_UPDATE_TIME_VISITS', "2000");
define('TYPE_JOOMLAWATCH_UPDATE_TIME_VISITS', "number");

define('JOOMLAWATCH_UPDATE_TIME_STATS', "4000");
define('TYPE_JOOMLAWATCH_UPDATE_TIME_STATS', "number");

define('JOOMLAWATCH_MAXID_BOTS', 40);
define('TYPE_JOOMLAWATCH_MAXID_BOTS', "number");

define('JOOMLAWATCH_MAXID_VISITORS', 40);
define('TYPE_JOOMLAWATCH_MAXID_VISITORS', "number");

define('JOOMLAWATCH_LIMIT_BOTS', 5);
define('TYPE_JOOMLAWATCH_LIMIT_BOTS', "number");

define('JOOMLAWATCH_LIMIT_VISITORS', 20);
define('TYPE_JOOMLAWATCH_LIMIT_VISITORS', "number");

define('JOOMLAWATCH_TRUNCATE_VISITS', 40);
define('TYPE_JOOMLAWATCH_TRUNCATE_VISITS', "number");

define('JOOMLAWATCH_TRUNCATE_STATS',15);
define('TYPE_JOOMLAWATCH_TRUNCATE_STATS', "number");

define('JOOMLAWATCH_STATS_KEEP_DAYS', 365);
define('TYPE_JOOMLAWATCH_STATS_KEEP_DAYS', "number");

define('JOOMLAWATCH_TIMEZONE_OFFSET', '0');
define('TYPE_JOOMLAWATCH_TIMEZONE_OFFSET', "number");

define('JOOMLAWATCH_WEEK_OFFSET', -0.56547619);
define('TYPE_JOOMLAWATCH_WEEK_OFFSET', "number");

define('JOOMLAWATCH_DAY_OFFSET', 0.0416667);
define('TYPE_JOOMLAWATCH_DAY_OFFSET', "number");

define('JOOMLAWATCH_FRONTEND_HIDE_LOGO', 0);
define('TYPE_JOOMLAWATCH_FRONTEND_HIDE_LOGO', "checkbox");

define('JOOMLAWATCH_IP_STATS', 0);
define('TYPE_JOOMLAWATCH_IP_STATS', "checkbox");

define('JOOMLAWATCH_HIDE_ADS', 0);
define('TYPE_JOOMLAWATCH_HIDE_ADS', "checkbox");

define('JOOMLAWATCH_TOOLTIP_ONCLICK', 'On');
define('TYPE_JOOMLAWATCH_TOOLTIP_ONCLICK', "checkbox");

define('JOOMLAWATCH_SERVER_URI_KEY', 'REDIRECT_URL');

define('TYPE_JOOMLAWATCH_BLOCKING_MESSAGE', "text");

define('JOOMLAWATCH_TOOLTIP_WIDTH', 1000);
define('TYPE_JOOMLAWATCH_TOOLTIP_WIDTH', "number");

define('JOOMLAWATCH_TOOLTIP_HEIGHT', 768);
define('TYPE_JOOMLAWATCH_TOOLTIP_HEIGHT', "number");

define('JOOMLAWATCH_TOOLTIP_URL', "http://www.netip.de/search?query={ip}");

define('JOOMLAWATCH_IGNORE_URI', '');
define('TYPE_JOOMLAWATCH_IGNORE_URI', "text");

define('JOOMLAWATCH_TRUNCATE_GOALS', 20);

define('JOOMLAWATCH_FRONTEND_LINK', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_LINK', "checkbox");

define('JOOMLAWATCH_FRONTEND_COUNTRIES', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_COUNTRIES', "checkbox");

define('JOOMLAWATCH_FRONTEND_COUNTRIES_UPPERCASE', 0);
define('TYPE_JOOMLAWATCH_FRONTEND_COUNTRIES_UPPERCASE', "checkbox");

define('JOOMLAWATCH_FRONTEND_COUNTRIES_NUM', 5);
define('TYPE_JOOMLAWATCH_FRONTEND_COUNTRIES_NUM', "number");

define('JOOMLAWATCH_FRONTEND_VISITORS', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_VISITORS', "checkbox");

define('JOOMLAWATCH_FRONTEND_USER_LINK', '');

define('JOOMLAWATCH_CACHE_FRONTEND_COUNTRIES', "300");

define('JOOMLAWATCH_CACHE_FRONTEND_VISITORS', "300");

define('JOOMLAWATCH_CACHE_FRONTEND_USERS', "300");

define('JOOMLAWATCH_FRONTEND_VISITORS_TODAY', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_VISITORS_TODAY', "checkbox");

define('JOOMLAWATCH_FRONTEND_VISITORS_YESTERDAY', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_VISITORS_YESTERDAY', "checkbox");

define('JOOMLAWATCH_FRONTEND_VISITORS_THIS_WEEK', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_VISITORS_THIS_WEEK', "checkbox");

define('JOOMLAWATCH_FRONTEND_VISITORS_LAST_WEEK', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_VISITORS_LAST_WEEK', "checkbox");

define('JOOMLAWATCH_FRONTEND_VISITORS_THIS_MONTH', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_VISITORS_THIS_MONTH', "checkbox");

define('JOOMLAWATCH_FRONTEND_VISITORS_LAST_MONTH', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_VISITORS_LAST_MONTH', "checkbox");

define('JOOMLAWATCH_FRONTEND_VISITORS_TOTAL', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_VISITORS_TOTAL', "checkbox");

define('JOOMLAWATCH_FRONTEND_COUNTRIES_FIRST', 1);
define('TYPE_JOOMLAWATCH_FRONTEND_COUNTRIES_FIRST', "checkbox");

define('JOOMLAWATCH_FRONTEND_VISITORS_TOTAL_INITIAL', 0);

define('JOOMLAWATCH_LICENSE_ACCEPTED', 0);

define('JOOMLAWATCH_BLOCKING_MESSAGE', "Your IP address was blocked by JoomlaWatch, please contact the system administrator");

define('JOOMLAWATCH_SPAMWORD_LIST', "
adipex
advicer
baccarrat
blackjack
bllogspot
booker
byob
car-rental-e-site
car-rentals-e-site
carisoprodol
casino
casinos
chatroom
cialis
coolcoolhu
coolhu
credit-card-debt
credit-report-4u
cwas
cyclen
cyclobenzaprine
dating-e-site
day-trading
debt-consolidation
debt-consolidation-consultant
discreetordering
duty-free
dutyfree
equityloans
fioricet
flowers-leading-site
freenet-shopping
freenet
gambling-
hair-loss
health-insurancedeals-4u
homeequityloans
homefinance
holdem
holdempoker
holdemsoftware
holdemtexasturbowilson
hotel-dealse-site
hotele-site
hotelse-site
incest
insurance-quotesdeals-4u
insurancedeals-4u
jrcreations
levitra
macinstruct
mortgage-4-u
mortgagequotes
online-gambling
onlinegambling-4u
ottawavalleyag
ownsthis
palm-texas-holdem-game
paxil
penis
pharmacy
phentermine
poker-chip
poze
pussy
rental-car-e-site
ringtones
roulette 
shemale
slot-machine
texas-holdem
thorcarlson
top-site
top-e-site
tramadol
trim-spa
ultram
valeofglamorganconservatives
viagra
vioxx
xanax
zolus");

define('JOOMLAWATCH_SPAMWORD_BANS_ENABLED', 0);
define('TYPE_JOOMLAWATCH_SPAMWORD_BANS_ENABLED', "checkbox");

define('TYPE_JOOMLAWATCH_SPAMWORD_LIST', "largetext");


define('JOOMLAWATCH_LANGUAGE', 'english');
define('TYPE_JOOMLAWATCH_LANGUAGE', "select");

define('JOOMLAWATCH_LICENSE',

"<h2>General</h2>
This component inherits all the <a href='http://www.gnu.org/licenses/gpl-3.0.html' target='_blank'>GNU GPL license</a> terms and comes with absolutely NO WARRANTY. 
<ul>
<li>You agree to use it at your own responsibility, respecting all the laws applicable for your country and countries of your visitors</li>
<li>You agree to respect the privacy of your visitors by placing a notice into your <b>Privacy policy</b>, that your site uses this software.</li>
<li>You agree that you can be informed via an email you provided about the information concerning this software. This makes it easier when there is a need to inform you about the latest versions and/or possible security updates.</li>
</ul>
<h2>Ad-free license</h2>
<ul><li>To keep the development going, and to implement the new feature requests, what requires a lot of time and effort, the non-intrusive 3rd party 468x60px <b>advertisements</b> were put into the back-end section. This is the only way how to keep the component <b>free of charge</b> for everyone.</li>
<li>If you want to <b>get rid of these ads</b>, please obtain the <a href='http://www.codegravity.com/donate/' target='_blank'>Ad-free license</a> by making a fixed one-time donation per domain. Your <b>ads will be removed</b>, and your personal or company name will be <b>listed</b> on the <a href='http://www.codegravity.com/donate/' target='_blank'>donation page</a>. This way you'll also support the further development and make the features you would like to have there completed.</li>
<li>If you don't like this model, please use some older versions with older features, such as <a href='http://www.codegravity.com/component/option,com_remository/Itemid,26/func,startdown/id,18/' target='_blank'>1.2.5.</a></li>
</ul>
Enjoy using JoomlaWatch<br/>
Matej Koval, author<br/>
<a href='http://www.codegravity.com/' target='_blank'>www.codegravity.com</a><br/><span style='color: #cccccc'>Copyright &copy;2006-2009 by Matej Koval</span><br/>");

define('JOOMLAWATCH_CREDITS', "Here I would like to give thanks to: <br/><br/><ul>" .
"<h3>Contributors of translations:</h3>" .
"<li><b>Ivars Karklins</b> - <a href='http://www.madonassports.lv' target='_blank'>http://www.madonassports.lv</a> for <b>latvian</b> translation<br/><br/></li>" .
"<li><b>Fabio Tursi</b> for <b>italian</b> translation<br/><br/></li>" .
"<li><b>Topraxxx</b> for <b>turkish</b> translation<br/><br/></li>" .
"<li><b>Stanislaw Lenkiewicz</b> - <a href='http://www.lenkiewicz.eu' target='_blank'>http://www.lenkiewicz.eu</a> for <b>polish</b> translation<br/><br/></li>" .
"<li><b>Kaunas</b> - <a href='http://sraimund.net' target='_blank'>http://sraimund.net</a> for <b>lithuanian</b> translation<br/><br/></li>" .
"<li><b>Allan Hallengreen</b> - <a href='http://cms-solution.dk' target='_blank'>http://cms-solution.dk</a> for <b>danish</b> translation<br/><br/></li>" .
"<li><b>Petr Houba</b> - <a href='http://www.folktime.cz' target='_blank'>http://www.folktime.cz</a> for <b>czech</b> translation<br/><br/></li>" .
"<li><b>JOKR Solutions</b> - <a href='http://www.jokrsolutions.se' target='_blank'>http://www.jokrsolutions.se</a> for <b>swedish</b> translation<br/><br/></li>" .
"<li><b>George Papadopoulos</b> - <a href='http://www.netlook.gr' target='_blank'>http://www.netlook.gr</a> for <b>greek</b> translation<br/><br/></li>" .
"<li><b>iPo</b> - <a href='http://www.mrt.si' target='_blank'>http://www.mrt.si</a> for <b>slovenian</b> translation<br/><br/></li>" .
"<li><b>Jacobo Villarreal</b> - <a href='mailto:jacobovillarreal at gmail.com' target='_blank'>jacobovillarreal at gmail .com</a> for <b>spanish</b> translation<br/><br/></li>" .
"<li><b>Dick Bronder</b> - <a href='http://www.dbws.nl' target='_blank'>www.dbws.nl</a> for <b>dutch</b> translation<br/><br/></li>" .
"<li><b>7P-Darkman</b> - <a href='http://hubpantanal.no-ip.info' target='_blank'>hubpantanal.no-ip.info</a> for <b>brazilian/portuguese</b> translation<br/><br/></li>" .
"<li><b>Stephane Couderc</b> - <a href='http://www.cours-svt.fr' target='_blank'>www.cours-svt.fr</a> for <b>french</b> translation<br/><br/></li>" .
"<li><b>Vitaliy K.</b> - <a href='http://psa-club.ru' target='_blank'>psa-club.ru</a> for <b>russian</b> translation<br/><br/></li>" .
"<li><b>Andreas Hofelich</b> - <a href='http://www.andreashofelich.de' target='_blank'>www.andreashofelich.de</a> for <b>german</b> translation<br/><br/></li>" .
"<li><b>myself :)</b> - for <b>slovak</b> translation<br/><br/></li>" .
"<h3>Also:</h3>" .
"<li>All sponsors, supporters and <a href='http://www.codegravity.com/donate/' target='_blank'>donators</a><br/><br/></li>" .
"<li>All JoomlaWatch users and fans for nice ideas and suggestions that helped making this software better with every version<br/><br/></li>" .
"<li><b>Niall Doherty</b> for the <a href='http://www.ndoherty.com/demos/coda-slider/1.1.1/' target='_blank'>Coda Slider</a> used in the settings<br/><br/></li>" .
"<li><b>Pascal Toussaint</b> for the <a href='http://www.pascalz.com/' target='_blank'>ip2country class</a><br/><br/></li>" .
"<li><a href='http://ip-to-country.webhosting.info/downloads/' target='_blank'>http://ip-to-country.webhosting.info/downloads/</a> for the IP to Country database<br/><br/>" .
"<li>Joomla Extensions directory - <a href='http://extensions.joomla.org' target='_blank'>http://extensions.joomla.org</a>, it's users for writing nice reviews<br/><br/>" .
"<li>Also other Joomla-related websites featuring new components, such as: <br/>" .
"<a href='http://www.joomlaos.de' target='_blank'>http://www.joomlaos.de</a> <br/> " .
"<a href='http://www.joomlaportal.de' target='_blank'>http://www.joomlaportal.de</a> <br/> " .
"<a href='http://www.joomladirectory.co.uk' target='_blank'>http://www.joomladirectory.co.uk</a> and <br/>" .
"<a href='http://www.joomla-erweiterungen.de' target='_blank'>http://www.joomla-erweiterungen.de</a>" .
"</ul><br/><h3>Thank you!</h3>");

define('DB_KEY_BROWSER',1);
define('DB_KEY_COUNTRY',2);
define('DB_KEY_GOALS',3);
define('DB_KEY_HITS',4);
define('DB_KEY_INTERNAL',5);
define('DB_KEY_IP',6);
define('DB_KEY_KEYWORDS',7);
define('DB_KEY_LOADS',8);
define('DB_KEY_OS',9);
define('DB_KEY_REFERERS',10);
define('DB_KEY_UNIQUE',11);
define('DB_KEY_URI',12);
define('DB_KEY_USERS',13);


$keysArray = array('uri', 'country', 'referers', 'ip', 'internal', 'users', 'goals', 'keywords', 'browser', 'os', 'ip_blocking_title');


?>