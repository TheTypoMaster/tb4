<?php

if( $this->recipients)
{
	echo '<ul class="autocompleter-choices" id="recipient_options">';
	foreach( $this->recipients as $recipient )
	{
		echo'<li onclick="update_recipient(this.innerHTML)">['. htmlspecialchars($recipient->username) . '] ' .  htmlspecialchars($recipient->name) . '</li>';

	}
	echo '</ul>';
}
?>