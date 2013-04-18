<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="get" name="adminForm" id="adminForm">
    <table class="jfbcAdminTableFilters">
        <tr>
            <td class="jfbcAdminTableFiltersSearch">
                Filters
                <input type="text" name="search" id="search" value="<?php echo $this->lists['search'] ?>"
                       class="text_area" title="Title Filter"/>
                <button id="jfbcSubmitButton">Go</button>
                <?php
                $resetJavascript = "document.getElementById('search').value='';";
                $resetJavascript .= "this.form.submit();";
                ?>
                <button id="jfbcResetButton" onclick="<?php echo $resetJavascript; ?>">Reset</button>
            </td>
        </tr>
    </table>
    <table class="adminlist">
        <thead>
        <tr>
            <th>#</th>
            <th><input id="jToggler" type="checkbox" name="toggle" value=""/></th>
            <th><?php echo JHTML::_('grid.sort', 'FB ID', 'fb_request_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'FB User From', 'fb_user_from', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th class="title"><?php echo JHTML::_('grid.sort', 'Joomla User From', 'joomla_user_from', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'FB User To', 'fb_user_to', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th class="title"><?php echo JHTML::_('grid.sort', 'Joomla User To', 'joomla_user_to', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th class="title"><?php echo JHTML::_('grid.sort', 'Request ID', 'jfbc_request_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Status', 'status', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Created Date', 'created', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Modified Date', 'modified', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($this->rows)
            foreach ($this->rows as $key => $row):
                $toUser = JFactory::getUser($row->joomla_user_to);
                $fromUser = JFactory::getUser($row->joomla_user_from);

                
                    $toLink = JRoute::_("index.php?option=com_users&view=user&task=edit&cid[]=" . $row->joomla_user_to);
                    $fromLink = JRoute::_("index.php?option=com_users&view=user&task=edit&cid[]=" . $row->joomla_user_from);
                 //SC15
                 //SC16

                $toHref = '';
                $fromHref = '';
                if ($row->joomla_user_to)
                    $toHref = '<a target="_blank" href="' . $toLink . '">' . $toUser->name . '</a>';
                if ($row->joomla_user_from)
                    $fromHref = '<a target="_blank" href="' . $fromLink . '">' . $fromUser->name . '</a>';
                ?>

            <tr class="row<?php echo ($key % 2); ?>">
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $checked = JHTML::_('grid.id', $key, $row->id); ?></td>
                <td><?php echo $row->fb_request_id;?></td>
                <?php if ($row->fb_user_from != -1) { ?>
                <td><a target="_blank" href="http://www.facebook.com/profile.php?id=<?php print $row->fb_user_from; ?>"><img
                        src="https://graph.facebook.com/<?php echo $row->fb_user_from; ?>/picture?type=small"
                        width="50"/></a></td>
                <?php } else { ?>
                <td>Application</td>
                <?php } ?>
                <td><?php echo $fromHref;?></td>
                <td><a target="_blank"
                       href="http://www.facebook.com/profile.php?id=<?php print $row->fb_user_to; ?>"><img
                        src="https://graph.facebook.com/<?php echo $row->fb_user_to; ?>/picture?type=small" width="50"/></a>
                </td>
                <td><?php echo $toHref;?></td>
                <td>
                    <a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=request&task=edit&cid=' . $row->jfbc_request_id); ?>"><?php echo $row->jfbc_request_id;?></a>
                </td>
                <td><?php if ($row->status == 0) echo "Sent";
                else if ($row->status == 1) echo "Read";
                else if ($row->status == 2) echo "Expired";
                    ?>
                </td>
                <td><?php echo $row->created;?></td>
                <td><?php echo $row->modified;?></td>
                <td><?php echo $row->id; ?></td>
            </tr>
                <?php endforeach; ?>
        <tr>
            <td colspan="12">
                <div style="text-align:center">
                    <div style="margin: 0 5px;"><strong>Sent:</strong> Request has been sent to user.</div>
                    <div style="margin: 0 5px;"><strong>Read:</strong> User has acted upon request.</div>
                    <div style="margin: 0 5px;"><strong>Expired:</strong> Facebook removes un-read requests
                        automatically after 2 weeks.
                    </div>
                    <div><br/><strong>Note:</strong> Due to a bug in the Facebook API, Requests can not be marked as "Read" for users who have not authenticated (logged in through) your Facebook Application.<br/>
                        A bug report has been submitted, and we will release an update for JFBConnect when available.
                    </div>
                </div>
            </td>
        </tr>

        </tbody>
        <tfoot>
        <tr>
            <td colspan="12"><?php echo $this->page->getListFooter(); ?></td>
        </tr>
        </tfoot>
    </table>

    <input type="hidden" name="option" value="com_jfbconnect"/>
    <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>"/>
    <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>"/>
    <input type="hidden" name="requestid" value="<?php echo JRequest::getVar('requestid');?>"/>
    <input type="hidden" name="fbuserto" value="<?php echo JRequest::getVar('fbuserto');?>"/>
    <input type="hidden" name="fbuserfrom" value="<?php echo JRequest::getVar('fbuserfrom');?>"/>
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
    <input type="hidden" name="boxchecked" value="0"/>
</form>
