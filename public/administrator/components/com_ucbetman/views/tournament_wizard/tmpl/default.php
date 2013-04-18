<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('uc_betman'), 'generic.png');
JToolBarHelper::preferences('com_uc_betman');


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
	  form.controller.value="tournament_templates_detail";
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
				<th class="name">
					<?php echo JHTML::_('grid.sort', 'Template Name', 'c.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
				
				<th class="short_name">
					<?php echo JHTML::_('grid.sort', 'Sport', 'c.sport', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				
				<th class="tournament_type">
					<?php echo JHTML::_('grid.sort', 'Tournament Type', 'c.tournament_type', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
				<th class="start_time">
					<?php echo JHTML::_('grid.sort', 'Start Time', 'start_time', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
	
				<th class="tournament_values">
					<?php echo JHTML::_('grid.sort', 'Tournament Values', 'c.tournament_values', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
	
				<th class="tournament_values_on">
					<?php echo JHTML::_('grid.sort', 'Tourn Values On', 'c.tournament_values_on', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
	
				<th class="bet_types">
					<?php echo JHTML::_('grid.sort', 'Bet Type Templates ', 'c.bet_type_template_ids', $this->lists['order_Dir'], $this->lists['order'] ); ?>
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
	
			$link 	= JRoute::_( 'index.php?option=com_sportman01&controller=tournament_templates_detail&task=edit&cid[]='. $row->id );
	
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
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->name;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit tournament_templates' ); ?>">
							<?php echo $row->name; ?></a>
					<?php
					}
					?>
				</td>
			
						<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->sport_id;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit tournament_templates' ); ?>">
							<?php echo $row->sport_id; ?></a>
					<?php
					}
					?>
				</td>
			
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tournament_type;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit tournament_templates' ); ?>">
							<?php echo $row->tournament_type; ?></a>
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
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit tournament_templates' ); ?>">
							<?php echo $row->start_time; ?></a>
					<?php
					}
					?>
				</td>
	
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tournament_values;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit tournament_templates' ); ?>">
							<?php echo $row->tournament_values; ?></a>
					<?php
					}
					?>
				</td>
				
						
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tournament_values_on;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit tournament_templates' ); ?>">
							<?php echo $row->tournament_values_on; ?></a>
					<?php
					}
					?>
				</td>	

				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->  	bet_type_template_ids;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit tournament_templates' ); ?>">
							<?php echo $row->  	bet_type_template_ids; ?></a>
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
			<td colspan="15">
				
			</td>
		</tfoot>
		</table>
	</div>
	
	<input type="hidden" name="controller" value="tournament_templates" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>