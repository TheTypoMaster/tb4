<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="componentheading">
	<?php echo $this->escape($this->message->title) ; ?>
</div>

<div class="message">
	<?php echo $this->escape($this->message->text) ; ?>
</div>
<?php if(isset($this->message->js)) : ?>
	<?php echo $this->message->js ?>
<?php endif ?>