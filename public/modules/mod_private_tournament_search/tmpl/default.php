<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

    <div class="private-tourn-right">
    	<div class="green-border-left">&nbsp;</div>
    	<div class="right-col-bottom-imgheader"><img src="templates/topbetta/images/col-head-private-tourn.gif" border="0" alt="Join Now For Free!"/></div>
         <div class="private-tourn-form">
        	<form class="private-tourn" action="/" name="private-tourn">

            	<input type="text" class="private-tourn-input" id="private-tourn-search" name="tournament_code" value=""/>
                <input type="submit" class="private-tourn-find"  name="private-tourn-submit" value="Find!"/>
                <input type="hidden" name="component" value="tournament" />
                <input type="hidden" name="task" value="searchprivatetournament" />
            </form>
        </div>
        <div class="private-tourn-links">
		<?php if($user->guest) :?>
			<a href="/user/register">
		<?php else : ?>
			<a rel="{handler: 'iframe', size: {x: 384, y: 486}}" href="/index.php?option=com_tournament&task=privatetournament&format=raw" class="modal">
		<?php endif ?>
			Create a tournament</a> | <a href="/content/article/9">What is this?</a>
        </div>
    </div><!-- close private-tourn-right -->