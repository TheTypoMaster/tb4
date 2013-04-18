<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <?php
        if($this->isCanvasIncorrect)
        {
            echo '<span style="color:#FF4444">Your Canvas application <strong>does not appear</strong> to be correctly configured. It is required for Requests to properly operate. '
                .'Please visit <a href="index.php?option=com_jfbconnect&controller=canvas">the Page Tab/Canvas configuration area</a> to correct the configuration.</span>';
        }
    ?>

	<table class="jfbcAdminTableFilters">
		<tr>
			<td class="jfbcAdminTableFiltersSearch">
				Filters
                <input type="text" name="search" id="search" value="<?php echo $this->lists['search'] ?>" class="text_area"	title="Title Filter" />
				<button id="jfbcSubmitButton">Go</button>
                <?php
				$resetJavascript = "document.getElementById('search').value='';";
                $resetJavascript .= "document.getElementById('filter_published').value='-1';";
				$resetJavascript .= "this.form.submit();";
			?>
				<button id="jfbcResetButton" onclick="<?php echo $resetJavascript; ?>">Reset</button>
			</td>
			<td class="jfbcAdminTableFiltersSelects">
                <?php echo $this->lists['published']; ?>
			</td>
		</tr>
	</table>
	<table class="adminlist">
		<thead>
			<tr>
				<th>#</th>
				<th><input id="jToggler" type="checkbox" name="toggle" value="" /></th>
                <th class="title"><?php echo JHTML::_('grid.sort', 'Title', 'title', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', 'Published', 'published', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', 'Send Count', 'send_count', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
                <th><?php echo JHTML::_('grid.sort', 'Destination URL', 'destination_url', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
                <th><?php echo JHTML::_('grid.sort', 'Breakout Canvas', 'breakout_canvas', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
                <th><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
        if ($this->rows)
            foreach ($this->rows as $key => $row): ?>
			<tr class="row<?php echo ($key%2); ?>">
				<td><?php echo $key+1; ?></td>
                <td><?php echo $checked = JHTML::_('grid.id', $key, $row->id); ?></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=request&task=edit&cid='.$row->id); ?>"><?php echo $row->title;?></a></td>
                <td class="center"><?php echo JHTML::_('grid.published', $row, $key ) ?></td>
                <td><a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=notification&task=display&requestid='.$row->id);?>"><?php echo $row->send_count?></a></td>
                <td><?php echo $row->destination_url;?></td>
                <td>
                    <?php if($row->breakout_canvas)
                    { ?>
                        <a title="Disable Breakout Canvas" onclick="return listItemTask('cb<?php echo $key;?>','disable_breakout_canvas')" href="javascript:void(0);">
		                    <img border="0" alt="Breakout Canvas Enabled" src="components/com_jfbconnect/assets/images/icon-16-allow.png">
                        </a>
                    <?php } else { ?>
                        <a title="Enable Breakout Canvas" onclick="return listItemTask('cb<?php echo $key;?>','enable_breakout_canvas')" href="javascript:void(0);">
                            <img border="0" alt="Breakout Canvas Disabled" src="components/com_jfbconnect/assets/images/icon-16-deny.png">
                        </a>
                    <?php } ?>
                </td>
				<td><?php echo $row->id; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->page->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="option" value="com_jfbconnect" />
	<input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
	<input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>
