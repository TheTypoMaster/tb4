<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

function showColumn($element)
{
	return ($element{0} != "_" && $element != "id" && $element != "fb_user_id");
}

$items = $this->_models[strtolower('UserMap')]->getList();
$row = JTable::getInstance('UserMap', 'Table');
$columns = array_filter(array_keys(get_object_vars($row)), "showColumn");

include_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jfbconnect'.DS.'models'.DS.'usermap.php');
?>

<form action="index.php" method="post" id="adminForm" name="adminForm">
<div id="editcell">
    	<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
			<?php
				$resetJavascript = "document.getElementById('search').value='';";
				foreach(array_keys($this->lists) as $key)
				{
					if($key != 'search')
					{
						$resetJavascript .= "this.form.getElementById('".$key."').value='';";
					}
				}

				$resetJavascript .= "this.form.submit();";
			?>
			<button onclick="<?php echo $resetJavascript; ?>"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
		<?php
			foreach(array_keys($this->lists) as $key)
			{
                if($key != 'search' && $key != 'order' && $key != 'order_Dir')
				{
					echo $this->lists[$key];
				}
			}
		?>
		</td>
	</tr>
	</table>
	<table class="adminlist">
	<thead>
		<tr>
            <th width="5"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($items); ?>);" />
			</th>
            <th><?php echo JHTML::_('grid.sort', 'Joomla User', 'j_user_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Facebook User', 'fb_user_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Sent Requests', 'sent', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Received Requests', 'received', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'FB App Authorized', 'authorized', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Created', 'created_at', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Updated', 'updated_at', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<?php
	$k = 0;
	$deletedMappings = false;
	for ($i = 0, $n = count($items); $i < $n; $i++)
	{
		$row = &$items[$i];
		$checked = JHTML::_('grid.id', $i, $row->id);

		$user = JFactory::getUser($row->j_user_id);
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a target="_blank" href="
                                    <?php
                                    
                                        print JRoute::_("index.php?option=com_users&view=user&task=edit&cid[]=".$row->j_user_id);
                                     //SC15
                                     //SC16
                                ?>
                                ">
                                <?php
                                print $user->name;
				?>
				</a>
			</td>
			<td align="center">
				<!-- <a target="_blank" href="http://www.facebook.com/profile.php?id=<?php print $row->fb_user_id; ?>"><?php print $row->fb_user_id; ?></a>-->
                <a target="_blank" href="http://www.facebook.com/profile.php?id=<?php print $row->fb_user_id; ?>"><img src="https://graph.facebook.com/<?php echo $row->fb_user_id; ?>/picture?type=small" width="50" /></a>

			</td>
            <td align="center"><a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=notification&task=display&fbuserfrom='.$row->fb_user_id);?>"><?php echo $row->sent?></a></td>
            <td align="center"><a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=notification&task=display&fbuserto='.$row->fb_user_id);?>"><?php echo $row->received?></a></td>
			<td align="center">
                <?php if ($row->authorized)
                    echo '<img src="components/com_jfbconnect/assets/images/icon-16-allow.png" />';
                else
                    echo '<img src="components/com_jfbconnect/assets/images/icon-16-deny.png" />';
                ?>
			</td>
            <td><?php print $row->created_at; ?></td>
			<td><?php print $row->updated_at; ?></td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</table>
</div>

<input type="hidden" name="option" value="com_jfbconnect" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="permtype" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<input type="hidden" name="view" value="usermap" />
</form>
