<?php
defined('_JEXEC') or die('Restricted access');
// JToolBarHelper::title(JText::_('uc_betman'), 'generic.png');
JToolBarHelper::preferences('com_ucbetman');


//Ordering allowed ?
$ordering = ($this->lists['order'] == 'ordering');

//onsubmit="return submitform();"

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');

?>

<script language="javascript" type="text/javascript">
/**
* Submit the admin form
* 
* small hack: let task desides where it comes
*/
function submitform(pressbutton){
var form = document.adminForm;
   if (pressbutton)
    {form.task.value=pressbutton;}
     
	 if ((pressbutton=='add')||(pressbutton=='edit')||(pressbutton=='publish')||(pressbutton=='unpublish')
	 ||(pressbutton=='orderdown')||(pressbutton=='orderup')||(pressbutton=='saveorder')||(pressbutton=='remove') )
	 {
	  form.controller.value="race_wizard_detail";
	 }
	try {
		form.onsubmit();
		}
	catch(e){}
	
	form.submit();
}


</script>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" >
	<div id="editcell">
		<table class="adminlist">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
				</th>
				<th class="meeting_id">
					<?php echo JHTML::_('grid.sort', 'Tournament Name', 'c.tournament_name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
				<th class="meeting_id">
					<?php echo JHTML::_('grid.sort', 'Meeting ID', 'c.tab_meeting_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
				
				<th class="name">
					<?php echo JHTML::_('grid.sort', 'Meeting Name', 'c.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				
				<th class="type">
					<?php echo JHTML::_('grid.sort', 'Type/Sport', 'c.sport', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
				<th class="start_time">
					<?php echo JHTML::_('grid.sort', 'Start Time', 'start_time', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
				<th class="end_time">
					<?php echo JHTML::_('grid.sort', 'End Time', 'end_time', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>	
				<th class="prizeFormula">
					<?php echo JHTML::_('grid.sort', 'Prize Formula', 'c.prizeFormula', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
	
				<th class="game_play">
					<?php echo JHTML::_('grid.sort', 'Game Play', 'c.game_play', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
	
				<th class="tournament_value">
					<?php echo JHTML::_('grid.sort', 'Tournament Value', 'tournament_value', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				
				<th class="Current Entrants">
					<?php echo JHTML::_('grid.sort', 'Entrants', 'current_entrants', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
					
				<th width="5%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'Published', 'h.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>	
				</th>
				<th width="80" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',  'Order', 'ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			 	</th>		 
				<th width="1%">
					<?php echo JHTML::_('grid.order',  $this->items ); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',  'ID', 'id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>	
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row = &$this->items[$i];
	
			$link 	= JRoute::_( 'index.php?option=com_ucbetman&controller=race_wizard_detail&task=edit&cid[]='. $row->id );
	
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$published 	= JHTML::_('grid.published', $row, $i );		
	
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset( $i ); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td>
			
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('tournament_name'), $row->checked_out ) ) {
						echo $row->tournament_name;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->tournament_name; ?></a>
					<?php
					}
					?>
				</td>
			
			
			
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tab_meeting_id;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->tab_meeting_id; ?></a>
					<?php
					}
					?>
				</td>
			
						<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->name;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->name; ?></a>
					<?php
					}
					?>
				</td>
			
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->sport;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->sport; ?></a>
					<?php
					}
					?>
				</td>
			
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->start_time;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->start_time; ?></a>
					<?php
					}
					?>
				</td>
	
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->end_time;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->end_time; ?></a>
					<?php
					}
					?>
				</td>
				
						
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->prizeFormula;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->prizeFormula; ?></a>
					<?php
					}
					?>
				</td>	
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->game_play;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->game_play;?></a>
					<?php
					}
					?>
				</td>	
				
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tournament_value;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo 'BuyIn: $'.$row->tournament_value.' + Entry Fee: $'.$row->entryFee; ?></a>
					<?php
					}
					?>
				</td>	


				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->current_entrants;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit race_wizard' ); ?>">
							<?php echo $row->current_entrants;?></a>
					<?php
					}
					?>
				</td>	
			
				<td align="center">
					<?php echo $published;?>
				</td>
				<td class="order" colspan="2">
				<?php //DEVNOTE: I'm using here ternary operators to avoid confusing behavior
		          // if you sort 'order' descending and press move up it goes down and vice versa
		          //2008-14-07 orderUpIcon,orderDownIcon fixed a bug by Peter van Westen
							?>  		
					<span><?php echo $this->pagination->orderUpIcon( $i, ($row->catid == @$this->items[$i-1]->catid), $this->lists['order_Dir']!='desc'?'orderup':'orderdown', 'Move Up', $ordering ); ?></span>
					<span><?php echo $this->pagination->orderDownIcon( $i, $n, ($row->catid == @$this->items[$i+1]->catid), $this->lists['order_Dir']!='desc'?'orderdown':'orderup', 'Move Down', $ordering ); ?></span>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
	<tfoot>
			<td colspan="16">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tfoot>
		</table>
	</div>
	
	<input type="hidden" name="controller" value="race_wizard" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>