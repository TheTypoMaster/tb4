<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class BettingModelBetResultStatus extends SuperModel
{
	protected $_table_name = '#__bet_result_status';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'name' => array(
			'name' => 'Name',
			'type' => self::TYPE_STRING
		),
		'description' => array(
			'name' => 'Description',
			'type' => self::TYPE_STRING
		),
		'status_flag' => array(
			'name' => 'Status Flag',
			'type' => self::TYPE_INTEGER
		),
		'created_date' => array(
			'name' => 'Created Date',
			'type' => self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' => 'Updated Date',
			'type' => self::TYPE_DATETIME_UPDATED
		)
	);
	/**
	 * String ID for unresulted bets
	 *
	 * @var string
	 */
	const STATUS_UNRESULTED = 'unresulted';

	/**
	 * String ID for resulted bets
	 *
	 * @var string
	 */
	const STATUS_PAID = 'paid';

	/**
	 * String ID for partially refunded bets
	 *
	 * @var string
	 */
	const STATUS_PARTIAL_REFUND = 'partially-refunded';

	/**
	 * String ID for fully refunded bets
	 *
	 * @var string
	 */
	const STATUS_FULL_REFUND = 'fully-refunded';
	
	/**
	 * String ID for pending bets
	 *
	 * @var string
	 */
	const STATUS_PENDING = 'pending';
	
	/**
	 * Load a single bet result status record by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	
	public function getBetResultStatus($id)
	{
		return $this->load($id);
	}

	/**
	 * Load a list of bet result statuses by status. Defaults to active ones only.
	 *
	 * @param integer $status_id
	 * @return object
	 */
	public function getBetResultStatusByStatus($status_id = 1)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('status_flag', $status_id)
		), 	SuperModel::FINDER_LIST);
	}
	
	/**
	 * Get list of bet statuses, sorted by name
	 * @return array
	 */
	public function getBetResultStatusByName($name){
		static $status_list = null;
		
		if(is_null($status_list)){
			$list = $this->getBetResultStatusByStatus();
			
			foreach($list as $status){
				$status_list[$status->name] = $status;
			}
		}
		
		return $status_list[$name];
	}

	/**
	 * Load a list of bet result statuses by status. Defaults to active ones only. (API)
	 *
	 * @param integer $status_id
	 * @return object
	 */
	public function getBetResultStatusByStatusApi($status_id = 1)
	{
		$db =& $this->getDBO();
		$query = "SELECT * FROM `tbdb_bet_result_status` WHERE status_flag = ".$status_id ;
        $db->setQuery($query);
	    return $db->loadObjectList();
	}
	
	/**
	 * Get list of bet statuses, sorted by name (API)
	 * @return array
	 */
	public function getBetResultStatusByNameApi($name){
		static $status_list = null;
		
		if(is_null($status_list)){
			$list = $this->getBetResultStatusByStatusApi();
			
			foreach($list as $status){
				$status_list[$status->name] = $status;
			}
		}
		
		return $status_list[$name];
	}
}
