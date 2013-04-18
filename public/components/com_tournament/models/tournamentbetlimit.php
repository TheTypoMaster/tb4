<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentBetLimit extends JModel
{
  /**
   * Load a single bet limit record by ID.
   *
   * @param integer $id
   * @return object
   */
  public function getBetLimit($id)
  {
    $db =& $this->getDBO();
    $query =
      'SELECT
        id,
        tournament_id,
        bet_type_id,
        value
      FROM
        ' . $db->nameQuote('#__tournament_bet_limit') . '
      WHERE
        id = ' . $db->quote($id);

    $db->setQuery($query);
    return $db->loadObject();
  }

  /**
   * Load all bet limit records associated with a specific tournament
   * by tournament ID.
   *
   * @param integer $tournament_id
   * @return array
   */
  public function getBetLimitsByTournamentID($tournament_id = null)
  {
    if(empty($tournament_id)) {
      $tournament_id = $this->tournament_id;
    }

    $db =& $this->getDBO();
    $query =
      'SELECT
        id,
        tournament_id,
        bet_type_id,
        value
      FROM
        ' . $db->nameQuote('#__tournament_bet_limit') . '
      WHERE
        tournament_id = ' . $db->quote($tournament_id);

    $db->setQuery($query);
    return $db->loadObjectList('bet_type_id');
  }

  /**
   * Get a specific bet limit for a tournament
   *
   * @param integer $tournament_id
   * @param integer $bet_type_id
   * @return array
   */
  public function getBetLimitByTournamentIDAndBettypeID($tournament_id, $bet_type_id)
  {
    $db =& $this->getDBO();
    $query =
      'SELECT
        value
      FROM
        ' . $db->nameQuote('#__tournament_bet_limit') . '
      WHERE
        tournament_id = ' . $db->quote($tournament_id) . '
        AND bet_type_id = ' . $db->quote($bet_type_id);

    $db->setQuery($query);
    return $db->loadResult('value');
  }

  /**
   * Remove a bet limit record.
   *
   * @param integer $id
   * @return bool
   */
  public function deleteBetLimitByID($id)
  {
    $db =& $this->getDBO();
    $query =
      'DELETE FROM
        ' . $db->nameQuote('#__tournament_bet_limit') . '
      WHERE
        id = ' . $db->quote($id);

    $db->setQuery($query);
    return $db->query();
  }

  /**
   * Store a bet limit record. Will determine whether to insert or update based
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
   * Insert a new bet limit record.
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
      'INSERT INTO ' . $db->nameQuote('#__tournament_bet_limit') . ' (
        tournament_id,
        bet_type_id,
        value
      ) VALUES (
        ' . $db->quote($params['tournament_id']) . ',
        ' . $db->quote($params['bet_type_id']) . ',
        ' . $db->quote($params['value']) . '
      )';

    $db->setQuery($query);
    return $db->query();
  }

  /**
   * Update an existing bet limit record.
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
        ' . $db->nameQuote('#__tournament_bet_limit') . '
      SET
        tournament_id = ' . $db->quote($params['tournament_id']) . ',
        bet_type_id = ' . $db->quote($params['bet_type_id']) . ',
        value = ' . $db->quote($params['value']) . '
      WHERE
        id = ' . $db->quote($params['id']);

    $db->setQuery($query);
    return $db->query();
  }
}