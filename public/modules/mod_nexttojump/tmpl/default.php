<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php if ($is_homepage) : ?>
		<div class="next-to-jump">
			<div class="next-to-jump-head">Today's Racing - <i><strong>Next To Jump</strong></i></div>
			<?php if (count($next_to_jump_list,COUNT_RECURSIVE) == 3) : ?>
			<div>Race data is temporarily unavailable. Please check back later.</div>
			<?php else : ?>
		    <table class="next-to-jump-table">
		    	<tr>
		    	<?php foreach ($next_to_jump_list as $meeting_type => $race_list) : ?>
		        	<td class="next-to-jump-border">
		            	<table class="next-to-jump-info">
		                	<tr>
		                    	<td class="next-to-jump-info-head odd"><img src="templates/topbetta/images/icn_<?php echo $this->escape($meeting_type) ?>_sml.png" border="0" alt=""/> <?php echo $this->escape(ucwords($meeting_type)) ?></td>
		                    </tr>
		                    <?php for ($i=0; $i<5; $i++) : ?>
							<?php $row_class = ($i % 2 == 0 ? 'even' : 'odd' ); ?>
		                    <tr>
		                    	<td class="<?php echo $row_class ?>">
		                    		<div class="next-to-jump-item">
			                    	<?php if(isset($race_list[$i])) :?>
			                    		<?php $race = $race_list[$i]; ?>
			                    		<a href="<?php echo $this->escape($race['link']) ?>"><span class="jumps-time"><?php echo $this->escape($race['counter']) ?></span> - <?php echo $race['number'] . ' - ' . $this->escape($race['meeting_name']) ?></a>
			                    	<?php else : ?>
			                    		--
			                    	<?php endif; ?>
			                    	</div>
		                    	</td>
		                    </tr>
		                    <?php endfor; ?>
		                </table>
		            </td>
				<?php endforeach; ?>
				</tr>
		    </table>
		    <?php endif; ?>
		</div><!-- close next-to-jump -->		
<?php else : ?>
	<div id="next_to_jump_button"><a href="#" onclick="return false;">NEXT TO JUMP</a></div>
	<div class="jump-flyout" id="next_to_jump">
		<?php if(empty($next_to_jump_list)) : ?>
		<div class="data-unavail">Race data is temporarily unavailable. Please check back later.</div>
		<?php else : ?>
		<table id="next_to_jump_table">
			<?php $i= 1; ?>
			<?php foreach ($next_to_jump_list as $race) : ?>
			<?php $row_class = ($i % 2 == 0 ? 'odd' : 'even' ); ?>
			<?php $link = '/betting/racing/meeting/' . $race->meeting_id . '/' . $race->number ?>
			<tr>
				<td class="<?php echo $row_class ?>"><img src="templates/topbetta/images/icn_<?php echo $this->escape(strtolower($race->competition_name)) ?>_sml.png" border="0" alt=""/></td>
				<td class="<?php echo $row_class ?>"><a href="<?php echo $this->escape($link) ?>"><?php echo $this->escape($race->meeting_name) ?></a></td>
				<td class="<?php echo $row_class ?>"><?php echo $this->escape($race->number) ?></td>
				<td class="<?php echo $row_class ?>"><?php echo date('H:i', strtotime($race->start_date)) ?></td>
			</tr>
			<?php $i++; ?>
			<?php endforeach; ?>
		</table>
		<?php endif; ?>
	</div><!-- close jump-flyout -->
<?php endif; ?>
