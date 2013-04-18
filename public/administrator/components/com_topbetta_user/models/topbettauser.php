<?php
/**
 * Joomla! 1.5 component topbetta user
 *
 * @version $Id: payment.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TopbettaUserModelTopbettaUser extends JModel
{
  /** @var array of transaction objects */
  private $_users = null;
  /** @var int total number of requests */
  private $_total = null;
  /** @var Jpagination object */
  private $_pagination = null;
  /** @var array option array */
  public $options = array(
    'title' => array(
      'Mr' => 'Mr',
      'Mrs' => 'Mrs',
      'Ms' => 'Ms',
      'Miss' => 'Miss',
      'Dr' => 'Dr',
      'Prof' => 'Prof',
    ),
    'day' => array(),
    'month' => array(),
    'year' => array(),
    'state' => array(
      'nsw' => 'New South Wales',
      'vic' => 'Victoria',
      'qld' => 'Queensland',
      'sa' => 'South Australia',
      'wa' => 'Western Australia',
      'nt' => 'Northern Territory',
      'act' => 'Australian Capital Territory',
      'tas' => 'Tasmania',
      'other' => 'Not in Australia'
    ),
    'heard_about' => array(
      'ATP' => 'Australia\'s Top Punter',
      'Friend' => 'Friend',
      'Word of mouth' => 'Word of Mouth',
      'Advertisement' => 'Advertisement',
      'TV Advertisement' => 'TV Advertisement',
      'Radio Advertisement' => 'Radio Advertisement',
      'Internet' => 'Internet',
      'Promotion' => 'Promotion',
      'Other' => 'Other',
    ),
    'status' => array(
      'active' => 'Active',
      'inactive' => 'Inactive',
    ),
    'identity_doc' => array(
      'birth' => 'Birth Certificate',
      'citizenship' => 'Citizenship Certificate',
      'passport' => 'Passport',
      'driver' => 'Driver\'s License',
    ),
  );

  /**
  * Constructor
  *
  * @return void
  */
  function __construct()
  {
    global $mainframe;
    parent::__construct();
    // Get the pagination request variables
    $limit = $mainframe->getUserStateFromRequest(
    'global.list.limit',
    'limit', $mainframe->getCfg('list_limit'));
    $limitstart = $mainframe->getUserStateFromRequest(
    $option.'limitstart', 'limitstart', 0);
    // Set the state pagination variables
    $this->setState('limit', $limit);
    $this->setState('limitstart', $limitstart);
  }

  /**
  * Builds a query to get data from #__topbetta_user
  *
  * @return string SQL query
  */
  private function _buildQuery()
  {
    $db =& $this->getDBO();
    $topbettaUserTable = $db->nameQuote('#__topbetta_user');
    $userTable = $db->nameQuote('#__users');
    $accountTransactionTable = $db->nameQuote('#__account_transaction');
    $tournamentTransactionTable = $db->nameQuote('#__tournament_transaction');
    $query = "SELECT t.*, u.email, u.username, u.block, u.registerDate, u.lastvisitDate, at.account_balance, tt.tournament_balance FROM $topbettaUserTable t"
      . " LEFT JOIN $userTable u ON u.id = t.user_id"
      . " LEFT JOIN (SELECT recipient_id, sum( amount ) AS account_balance FROM $accountTransactionTable"
      . " GROUP BY recipient_id) at ON u.id = at.recipient_id "
      . " LEFT JOIN (SELECT recipient_id, sum( amount ) AS tournament_balance FROM $tournamentTransactionTable"
      . " GROUP BY recipient_id) tt ON u.id = tt.recipient_id "
      . $this->_buildQueryWhere()
      . $this->_buildQueryOrderBy()
    ;
//print ($query);exit;
    return $query;
  }

  /**
  * Build the ORDER part of a query
  *
  * @return string part of an SQL query
  */
  private function _buildQueryOrderBy()
  {
    global $mainframe, $option;
    $db =& $this->_db;
    // Array of allowable order fields
    $orders = array('id', 'user_id', 'username', 'first_name', 'last_name', 'email', 'marketing_opt_in_flag', 'registerDate', 'lastvisitDate', 'account_balance', 'tournament_balance', 'self_exclusion_date');
    // Get the order field and direction, default order field
    // is 'ordering', default direction is ascending
    $filter_order = $mainframe->getUserStateFromRequest(
    $option.'filter_order', 'filter_order', 'id');
    $filter_order_Dir = strtoupper(
    $mainframe->getUserStateFromRequest(
    $option.'filter_order_Dir', 'filter_order_Dir', 'ASC'));
    // Validate the order direction, mus

    if($filter_order_Dir != 'ASC' && $filter_order_Dir != 'DESC')
    {
      $filter_order_Dir = 'ASC';
    }
    // If order column is unknown use the default

    if (!in_array($filter_order, $orders))
    {
      $filter_order = 'id';
    }

    $orderby = ' ORDER BY '. $db->nameQuote($filter_order) .' '.$filter_order_Dir;

    // Return the ORDER BY clause

    return $orderby;
  }


  /**
  * Builds the WHERE part of a query
  *
  * @return string Part of an SQL query
  */
  private function _buildQueryWhere()
  {
    global $mainframe, $option;
    $db =& $this->_db;
    // Get the filter values
    $filter_search = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_search', 'filter_topbettauser_search');
    $filter_state = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_state', 'filter_topbettauser_state');
    $filter_heard_about = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_heard_about', 'filter_topbettauser_heard_about');
    $filter_status = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_status', 'filter_topbettauser_status');
    $filter_marketing = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_marketing', 'filter_topbettauser_marketing');
    $filter_registration_from_date = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_registration_from_date', 'filter_topbettauser_registration_from_date');
    $filter_registration_to_date = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_registration_to_date', 'filter_topbettauser_registration_to_date');
    $filter_login_from_date = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_login_from_date', 'filter_topbettauser_login_from_date');
    $filter_login_to_date = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_login_to_date', 'filter_topbettauser_login_to_date');

    $registry =& JFactory::getConfig();
    $timezone_offset = $registry->getValue('config.offset');

    // Prepare the WHERE clause
    $where = array();
    // Determine search terms
    if ($filter_search = trim($filter_search))
    {
      $filter_search = JString::strtolower($filter_search);
      $filter_search = $db->getEscaped($filter_search);
      $orcond = array();
      $orcond[] = 'LOWER(t.first_name) LIKE "%'.$filter_search.'%"';
      $orcond[] = 'LOWER(t.last_name) LIKE "%'.$filter_search.'%"';
      $orcond[] = 'LOWER(t.street) LIKE "%'.$filter_search.'%"';
      $orcond[] = 'LOWER(t.city) LIKE "%'.$filter_search.'%"';
      $orcond[] = 'LOWER(t.postcode) LIKE "%'.$filter_search.'%"';
      $orcond[] = 'LOWER(t.source) LIKE "%'.$filter_search.'%"';
      $orcond[] = 'LOWER(u.email) LIKE "%'.$filter_search.'%"';
      $orcond[] = 'LOWER(u.username) LIKE "%'.$filter_search.'%"';
      if( ctype_digit($filter_search) )
      {

        $orcond[] = 'LOWER(t.msisdn) LIKE "%'.$filter_search.'%"';
        $orcond[] = 'LOWER(t.phone_number) LIKE "%'.$filter_search.'%"';
      }

      $where[] = '(' . implode( ' OR ', $orcond ) . ')';
    }

    if( $filter_state )
    {
      $where[] = 't.state = ' . $db->quote($filter_state);
    }

    if( $filter_heard_about )
    {
      $where[] = 't.heard_about = ' . $db->quote($filter_heard_about);
    }

    if( $filter_status )
    {
      $where[] = 'u.block = ' . $db->quote($filter_status == 'inactive' ? '1' : '0' );
    }

    if( $filter_marketing )
    {
      $where[] = 't.marketing_opt_in_flag = ' . $db->quote($filter_marketing == 'yes' ? '1' : '0' );
    }

	if( $filter_registration_from_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_registration_from_date, $m) )
	{
		$where[] = 'u.registerDate >= ' . $db->quote($filter_registration_from_date . ' 00:00:00') . " - INTERVAL $timezone_offset hour";
	}

	if( $filter_registration_to_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_registration_to_date, $m) )
	{
		$where[] = 'u.registerDate <= ' . $db->quote($filter_registration_to_date . ' 23:59:59') . " - INTERVAL $timezone_offset hour";
	}

  	if( $filter_login_from_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_login_from_date, $m) )
	{
		$where[] = 'u.lastvisitDate >= ' . $db->quote($filter_login_from_date . ' 00:00:00') . " - INTERVAL $timezone_offset hour";
	}

	if( $filter_login_to_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_login_to_date, $m) )
	{
		$where[] = 'u.lastvisitDate <= ' . $db->quote($filter_login_to_date . ' 23:59:59') . " - INTERVAL $timezone_offset hour";
	}

    // return the WHERE clause
    return (count($where)) ? ' WHERE '.implode(' AND ', $where) : '';
}

  /**
  * Get a list of transactions
  *
  * @param int transaction id
  * @return object
  */
  function getUser( $userId )
  {
    // Get the database connection
    $db =& $this->getDBO();
    $topbettaUserTable = $db->nameQuote('#__topbetta_user');
    $userTable = $db->nameQuote('#__users');

    if( empty($userId) || !ctype_digit($userId))
    {
      return null;
    }

    $query = "SELECT t.*, u.email, u.username, u.block, u.name FROM $topbettaUserTable t"
      . " LEFT JOIN $userTable u ON u.id = t.user_id"
      . " WHERE t.user_id = " . $db->quote($userId)
    ;

    $db->setQuery($query);
    // Return the transaction
    return $db->loadObject();
  }

    /**
  * Get a list of transactions
  * @param boolean the flag for csv list, which will ignore 'limit' and get all the transactions
  * @return array of objects
  */
  function getUsers( $isCsv = false )
  {
    // Get the database connection
    $db =& $this->_db;
    if( empty($this->_users) )
    {
      // Build query and get the limits from current state
      $query = $this->_buildQuery();
      if( $isCsv )
      {
        $limitstart = 0;
        $limit = 0;
      }
      else
      {
        $limitstart = $this->getState('limitstart');
        $limit = $this->getState('limit');
      }
      $this->_users = $this->_getList($query, $limitstart, $limit);
    }
    // Return the list of requests
    return $this->_users;
  }

  /**
  * Get a pagination object
  *
  * @return pagination object
  */
  function getPagination()
  {
    if (empty($this->_pagination))
    {
      // Import the pagination library
      jimport('joomla.html.pagination');
      // Prepare the pagination values
      $total = $this->getTotalPage();
      $limitstart = $this->getState('limitstart');
      $limit = $this->getState('limit');
      // Create the pagination object
      $this->_pagination = new JPagination($total,$limitstart,$limit);
    }
    return $this->_pagination;
  }


    /**
  * Get number of requests
  *
  * @return integer
  */
    function getTotalPage()
    {
      if (empty($this->_total))
    {
      //$query = $this->_buildQuery();
      //$this->_total = $this->_getListCount($query);
      //MC FIX - Was timing out doing this stupid count above
      $db =& $this->getDBO();
      $query = "SELECT COUNT(*) FROM " . $db->nameQuote('#__topbetta_user');
	  $db->setQuery($query);
	  $this->_total = $db->loadResult();
    }
    return $this->_total;
    }

    /**
  * Method to load options
  *
  * @return array
  */
  function loadDynamicOptions()
  {
    for($i=1; $i <= 31; $i++ )
    {
      $this->options['day'][$i] = sprintf('%02s', $i);
    }

    for( $i=1; $i <= 12; $i++ )
    {
      $this->options['month'][$i] = date('F', mktime(0,0,0,$i,1));
    }

    $currentYear = date('Y');
    $current18thYear = $currentYear - 18;
    for( $i= $current18thYear; $i >= 1900; $i-- )
    {
      $this->options['year'][$i] = $i;
    }
  }

  /**
   * Check if a field has already had the same value
   *
   * @param string field name
   * @param string value
   * @param int exclude user_id
   * @return boolean true if the values exists.
   */

  function isExisting( $field, $value, $userId = null )
  {
    $db =& $this->getDBO();
    $table = $db->nameQuote('#__users');
    $field = $db->nameQuote($field);
    $value = $db->quote($value);

    $query = "SELECT * FROM $table WHERE $field = $value";
    if( $userId )
    {
      $query .= " AND id != " . $db->quote($userId);
    }

    $query .= " LIMIT 1";
    $db->setQuery($query);

    return (bool)$db->loadObject();
  }

  /**
   * Method to store the user data
   *
   * @param array data array
   * @return	boolean	True on success
   */
  function store($params)
  {
    $db =& Jfactory::getDBO();

    $userId = $db->quote( $params['user_id'] );
    $title = $db->quote( $params['title'] );
    $first_name = $db->quote( $params['first_name'] );
    $last_name = $db->quote( $params['last_name'] );
    $street = $db->quote( $params['street'] );
    $city = $db->quote( $params['city'] );
    $state = $db->quote( $params['state'] );
    $country = $db->quote( $params['country'] );
    $postcode = $db->quote( $params['postcode'] );
    $dob_day = $db->quote( $params['dob_day'] );
    $dob_month = $db->quote( $params['dob_month']);
    $dob_year = $db->quote( $params['dob_year'] );
    $msisdn = $db->quote( $params['msisdn'] );
    $phone_number = $db->quote( $params['phone_number']);
    $promo_code = $db->quote( $params['promo_code'] );
    $heard_about = $db->quote( $params['heard_about'] );
    $heard_about_info = $db->quote( $params['heard_about_info'] );
    $marketing_opt_in_flag = $db->quote( $params['marketing_opt_in_flag']);
    $email_jackpot_reminder_flag = $db->quote( $params['email_jackpot_reminder_flag']);
    $source = $db->quote( $params['source']);
    $self_exclusion_date = empty($params['self_exclusion_date']) ? 'null' : $db->quote($params['self_exclusion_date']);
    $bet_limit = $db->quote($params['bet_limit']);
    $requested_bet_limit = $db->quote($params['requested_bet_limit']);

    // identity and bank details
    $identity_verified_flag = $db->quote($params['identity_verified_flag']);
    $identity_doc = $db->quote($params['identity_doc']);
    $identity_doc_id = $db->quote($params['identity_doc_id']);
    $bsb_number = $db->quote($params['bsb_number']);
    $bank_account_number = $db->quote($params['bank_account_number']);
    $account_name = $db->quote($params['account_name']);
    $bank_name = $db->quote($params['bank_name']);

    $table = $db->nameQuote('#__topbetta_user');

    $checkQuery = "SELECT * FROM $table WHERE user_id = " . $userId . " LIMIT 1";
    $db->setQuery($checkQuery);
    if( $db->query())
    {
      //update existing record
      $query = "UPDATE $table SET user_id = $userId, title = $title, first_name = $first_name, last_name = $last_name,
             street = $street, city = $city, state = $state, country = $country, postcode = $postcode,
             dob_day = $dob_day, dob_month = $dob_month, dob_year = $dob_year, msisdn = $msisdn, phone_number = $phone_number,
             promo_code = $promo_code, heard_about = $heard_about, heard_about_info = $heard_about_info,
             marketing_opt_in_flag = $marketing_opt_in_flag, identity_verified_flag = $identity_verified_flag,
             identity_doc = $identity_doc, identity_doc_id = $identity_doc_id,
             bsb_number = $bsb_number, bank_account_number = $bank_account_number, account_name = $account_name,
             bank_name = $bank_name, email_jackpot_reminder_flag = $email_jackpot_reminder_flag, source = $source,
             self_exclusion_date = $self_exclusion_date, bet_limit = $bet_limit, requested_bet_limit = $requested_bet_limit
             WHERE user_id = $userId
      ";
    }
    else
    {
      //insert record
      $query = "INSERT INTO $table
        ( user_id, title, first_name, last_name, street, city, state, country, postcode, dob_day, dob_month, dob_year,
        msisdn, phone_number, promo_code, heard_about, heard_about_info, marketing_opt_in_flag, identity_verified_flag, identity_doc, identity_doc_id,
        bsb_number, bank_account_number, account_name, bank_name, email_jackpot_reminder_flag, source, self_exclusion_date, bet_limit, requested_bet_limit)
        VALUES( $userId, $title, $first_name, $last_name, $street, $city, $state, $country, $postcode, $dob_day, $dob_month, $dob_year,
        $msisdn, $phone_number, $promo_code, $heard_about, $heard_about_info, $marketing_opt_in_flag, $identity_verified_flag,
        $bsb_number, $bank_account_number, $account_name, $bank_name, $email_jackpot_reminder_flag, $source, $self_exclusion_date, $bet_limit, $requested_bet_limit )
      ";
    }
    $db->setQuery($query);

    if(!$db->query())
    {
      return false;
    }

    return true;
  }

  function getUserBalance( $type, $userId = null )
  {
    $db =& Jfactory::getDBO();

    switch( $type )
    {
    	case 'account':
    		$transactionTable = $db->nameQuote('#__account_transaction');
    		break;
    	case 'tournament':
    		$transactionTable = $db->nameQuote('#__tournament_transaction');
    		break;
    	default:
    		return null;
    }

  	$query = "SELECT sum( amount ) AS balance FROM $transactionTable";

  	if( !empty($userId) )
  	{
  		$query .= " WHERE recipient_id = " . $db->quote( $userId );
    	$query .= " GROUP BY recipient_id";
  	}

  	$db->setQuery($query);

    return $db->loadResult();
  }

}
?>