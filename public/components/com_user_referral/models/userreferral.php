<?php
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * User Referral Component User Referral Model
 *
 * @package		Joomla
 * @subpackage	User
 * @since 1.5
 */
class UserReferralModelUserReferral extends JModel
{
  /**
   * Get a referral record by friend_id
   *
   * @param int friend id
   * @return object
   */
  public function getReferralByFriendId($friend_id)
  {
    $db =& $this->getDBO();
    $query =
      'SELECT
        id,
        referrer_id,
        friend_id,
        paid_flag,
        created_date,
        updated_date
      FROM
        ' . $db->nameQuote('#__user_referral') . '
      WHERE
        friend_id = ' . $db->quote($friend_id) . '
        LIMIT 1';
    
    $db->setQuery($query);
    return $db->loadObject();
  }
  
  
  /**
   * Store a referral record. Will determine whether to insert or update based on the
   * presence of an ID.
   *
   * @param array $params
   * @return bool
   */
  public function store( $params )
  {
    $db =& $this->getDBO();

    if(empty($params['id']))
    {
      $result = $this->_insert($params, $db);
    }
    else
    {
      $result = $this->_update($params, $db);
    }

    return $result;
  }

  /**
   * Insert a new referral record.
   *
   * @param array $params
   * @param JDatabase $db
   * @return bool
   */
  private function _insert( $params, $db = null )
  {
    if(is_null($db))
    {
      $db =& $this->getDBO();
    }

    $query = 'INSERT INTO ' . $db->nameQuote('#__user_referral') . ' (
	  	referrer_id,
		friend_id,
		tournament_transaction_id,
    	paid_flag,
    	created_date,
    	updated_date
    	) VALUES (
    	'. $db->quote($params['referrer_id']) . ',
    	'. $db->quote($params['friend_id']) . ',
    	'. ($params['tournament_transaction_id'] === null ? 'NULL' : $db->quote($params['tournament_transaction_id'])) . ',
    	'. $db->quote($params['paid_flag']) . ',
    	NOW(),
    	NOW()
    )';
    $db->setQuery($query);
    $db->query();

    return $db->insertId();
  }

  /**
   * Update an existing referral record.
   *
   * @param array $params
   * @param JDatabase $db
   * @return bool
   */
  private function _update( $params, $db = null )
  {
    if(is_null($db)) {
      $db =& $this->getDBO();
    }

    $query =
      'UPDATE
        ' . $db->nameQuote('#__user_referral') . '
      SET
      	referrer_id = ' . $db->quote($params['referrer_id']) . ',
      	friend_id = ' . $db->quote($params['friend_id']) . ',
      	tournament_transaction_id = ' . ($params['tournament_transaction_id'] === null ? 'NULL' : $db->quote($params['tournament_transaction_id'])) . ',
      	paid_flag = ' . $db->quote($params['paid_flag']) . ',
      	updated_date = NOW()
      WHERE
        id = ' . $db->quote($params['id']);

    $db->setQuery($query);
    return $db->query();
  }

}
?>
