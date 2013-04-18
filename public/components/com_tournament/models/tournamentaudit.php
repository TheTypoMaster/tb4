<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentAudit extends JModel
{
  /**
   * Store an audit record of a change made to one of the tournament fields
   *
   * @param array $params
   * @return bool
   */
  public function store($params)
  {
    $db =& $this->getDBO();
    $result = $this->_insert($params, $db);

    return $result;
  }

  /**
   * Insert a new audit record.
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
      'INSERT INTO ' . $db->nameQuote('#__tournament_audit') . ' (
        tournament_id,
        field_name,
        old_value,
        new_value,
        update_date
      ) VALUES (
        ' . $db->quote($params['tournament_id']) . ',
        ' . $db->quote($params['field_name']) . ',
        ' . $db->quote($params['old_value']) . ',
        ' . $db->quote($params['new_value']) . ',
        NOW()
      )';

    $db->setQuery($query);
    return $db->query();
  }

}