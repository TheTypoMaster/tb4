<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JFBConnectModelRequest extends JModel
{
    var $_pagination = null;

    function __construct()
    {
        parent::__construct();
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int)$array[0]);
    }

    function setId($id)
    {
        $this->_id = $id;
        $this->_data = null;
    }

    function &getData($id = null)
    {
        if (empty($this->_data))
        {
            $query = 'SELECT * FROM #__jfbconnect_request' .
                     ' WHERE id = ' . $this->_db->quote($id);

            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if (!$this->_data)
        {
            $this->_data = new stdClass();
            $this->_data->id = 0;
            $this->_data->published = 0;
            $this->_data->title = "";
            $this->_data->message = "";
            $this->_data->send_count = 0;
            $this->_data->destination_url = "";
            $this->_data->thanks_url = "";
            $this->_data->breakout_canvas = false;
            $this->_data->created = JFactory::getDate()->toMySQL();
            $this->_data->modified = null;
        }
        return $this->_data;
    }
}