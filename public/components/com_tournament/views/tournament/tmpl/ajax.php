<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php switch($this->type) :?>
<?php case 'sport': ?>
	<?php echo join('_|_', $this->sport_options)?>
<?php break ?>
<?php case 'competition': ?>
	<?php echo join('_|_', $this->competition_options)?>
<?php break ?>
<?php case 'eventgroup' : ?>
	<?php echo join('_|_', $this->event_group_options)?>
<?php break;?>
<?php endswitch ?>
