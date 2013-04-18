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
	  form.controller.value="user_manager_detail";
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
				<th class="user_id">
					<?php echo JHTML::_('grid.sort', 'jID', 'user_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
					
				<th class="pin">
					<?php echo JHTML::_('grid.sort', 'Username', 'username', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
				<th class="name">
					<?php echo JHTML::_('grid.sort', 'First Name', 'tb_namef', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				
				<th class="name">
					<?php echo JHTML::_('grid.sort', 'Surname', 'tb_namel', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>

				<th class="name">
					<?php echo JHTML::_('grid.sort', 'Email', 'c.email', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>

				<th class="balance">
					<?php echo JHTML::_('grid.sort', 'Address', 'tb_address', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				
				<th class="freebalance">
					<?php echo JHTML::_('grid.sort', 'Suburb', 'tb_suburb', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>

				<th class="mobile">
					<?php echo JHTML::_('grid.sort', 'State', 'tb_state', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>

				<th class="hear">
					<?php echo JHTML::_('grid.sort', 'Heard by', 'tb_howuhear', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				
				<th class="register_date">
					<?php echo JHTML::_('grid.sort', 'Register Date', 'register_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
				</th>
				<th class="last_visit">
					<?php echo JHTML::_('grid.sort', 'Last Visit', 'last_visit', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					
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
	
			$link 	= JRoute::_( 'index.php?option=com_ucbetman&controller=user_manager_detail&task=edit&cid[]='. $row->id );
	
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
						echo $row->id;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->id; ?></a>
					<?php
					}
					?>
				</td>
			
						<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->username;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->username; ?></a>
					<?php
					}
					?>
				</td>
			
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tb_namef;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->tb_namef; ?></a>
					<?php
					}
					?>
				</td>
			
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tb_namel;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->tb_namel; ?></a>
					<?php
					}
					?>
				</td>

				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->email;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->email; ?></a>
					<?php
					}
					?>
				</td>
					
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tb_address;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->tb_address; ?></a>
					<?php
					}
					?>
				</td>
				
						
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tb_suburb;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->tb_suburb; ?></a>
					<?php
					}
					?>
				</td>	
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tb_state;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->tb_state;?></a>
					<?php
					}
					?>
				</td>	
				
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->tb_howuhear;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->tb_howuhear;?></a>
					<?php
					}
					?>
				</td>	


				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->registerDate;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->registerDate;?></a>
					<?php
					}
					?>
				</td>	
			
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) {
						echo $row->lastvisitDate;
					} else {
					?>
						<a href="<?php echo $link; ?>" name="<?php echo JText::_( 'Edit user_manager' ); ?>">
							<?php echo $row->lastvisitDate;?></a>
					<?php
					}
					?>
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
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tfoot>
		</table>
	</div>
	
	<input type="hidden" name="controller" value="user_manager" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>