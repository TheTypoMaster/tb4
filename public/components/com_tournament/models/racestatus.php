<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelRaceStatus extends JModel
{
  /**
   * Load a single record from the #__race_status table by ID.
   *
   * @param integer $id
   * @return object
   */

	public function getRaceStatus($id)
	{
	  $db =& $this->getDBO();
      $query =
	      'SELECT
	        id,
	        keyword,
	        name,
	        description
	      FROM
	        ' . $db->nameQuote('#__race_status') . '
	      WHERE
	        id = ' . $db->quote($id);

	    $db->setQuery($query);
	    return $db->loadObject();

	}
	
  /**
   * Get race status id by keyword
   *
   * @param string $keyword
   * @return integer
   */
	
	public function getRaceStatusIdByKeyword($keyword)
	{
	  $db =& $this->getDBO();
      $query =
	      'SELECT
	        id
	      FROM
	        ' . $db->nameQuote('#__race_status') . '
	      WHERE
	        keyword = ' . $db->quote($keyword);
      
	    $db->setQuery($query);
	    
	    return $db->loadResult();
	}
	
   /**
   * Get race status list
   *
   * @return object
   */
	
	public function getRaceStatusList($key='keyword')
	{
	  $db =& $this->getDBO();
      $query =
	      'SELECT
	        id,
	        keyword,
	        name,
	        description
	      FROM
	        ' . $db->nameQuote('#__race_status');
      
	    $db->setQuery($query);
	    
	    return $db->loadObjectList($key);
	}
	

  /**
   * Store a race status record. Will determine whether to insert or update based
   * on the presence of an ID.
   *
   * @param array $params
   * @return bool
   */
  public function store($params)
  {
    $db =& $this->getDBO();
    if(empty($params['id'])) {
      $result = $this->_insert($params, $db);
    } else {
      $result = $this->_update($params, $db);
    }

    return $result;
  }

  /**
   * Insert a new race status record.
   *
   * @param array $params
   * @param JDatabase $db
   * @return bool
   */
  private function _insert($params, $db = null)
  {
    if(is_null($db)) {
      $db =& $this->getDBO();
    }

    $query =
      'INSERT INTO ' . $db->nameQuote('#__race_status') . ' (
        id,
        keyword,
        name,
        description
      ) VALUES (
        ' . $db->quote($params['id']) . ',
        ' . $db->quote($params['keyword']) . ',
        ' . $db->quote($params['name']) . ',
        ' . $db->quote($params['description']) . '
      )';

    $db->setQuery($query);
    $db->query();

    return $db->insertId();
  }

  /**
   * Update an existing race status record.
   *
   * @param array $params
   * @param JDatabase $db
   * @return bool
   */
  private function _update($params, $db = null)
  {
    if(is_null($db)) {
      $db =& $this->getDBO();
    }

    $query =
      'UPDATE
        ' . $db->nameQuote('#__race_status') . '
      SET
        id = ' . $db->quote($params['id']) . ',
        keyword = ' . $db->quote($params['keyword']) . ',
        name = ' . $db->quote($params['name']) . ',
        description = ' . $db->quote($params['description']) . '
      WHERE
        id = ' . $db->quote($params['id']);

    $db->setQuery($query);
    return $db->query();
  }

}

?>