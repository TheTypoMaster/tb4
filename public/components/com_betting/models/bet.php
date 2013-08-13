<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

/**
 * Betting bet Model
 */
class BettingModelBet extends SuperModel
{
	protected $_table_name = '#__bet';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'external_bet_id' => array(
			'name' => 'External Bet ID',
			'type' => self::TYPE_INTEGER
		),
		'user_id' => array(
			'name' => 'User ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_amount' => array(
			'name' => 'Bet Amount',
			'type' => self::TYPE_INTEGER
		),
		'bet_type_id' => array(
			'name' => 'Bet Type ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_result_status_id' => array(
			'name' => 'Bet Result Status ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_origin_id' => array(
			'name' => 'Bet Origin ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_product_id' => array(
			'name' => 'Bet Product ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_transaction_id' => array(
			'name' => 'Bet Transaction ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_freebet_transaction_id' => array(
			'name' => 'Free Bet Transaction ID',
			'type' => self::TYPE_INTEGER
		),
		'result_transaction_id' => array(
			'name' => 'Result Transaction ID',
			'type' => self::TYPE_INTEGER
		),
		'refund_transaction_id' => array(
			'name' => 'Refund Transaction ID',
			'type' => self::TYPE_INTEGER
		),
		'refund_freebet_transaction_id' => array(
			'name' => 'Free Bet Refund Transaction ID',
			'type' => self::TYPE_INTEGER
		),
		'resulted_flag' => array(
			'name' => 'Resulted Flag',
			'type' => self::TYPE_INTEGER
		),
		'refunded_flag' => array(
			'name' => 'Refunded Flag',
			'type' => self::TYPE_INTEGER
		),
		'flexi_flag' => array(
			'name' => 'Flexi Flag',
			'type' => self::TYPE_INTEGER
		),
		'fixed_odds' => array(
			'name' => 'Fixed Odds',
			'type' => self::TYPE_FLOAT
		),
		'created_date' => array(
			'name' => 'Created Date',
			'type' => self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' => 'Updated Date',
			'type' => self::TYPE_DATETIME_UPDATED
		),
		'external_bet_error_message' => array(
			'name' => 'External Bet Error Message',
			'type' => self::TYPE_STRING
		),
		'invoice_id' => array(
			'name' => 'BM Invoice ID',
			'type' => self::TYPE_STRING
		),
		'bet_freebet_flag' => array(
			'name' => 'Free Bet Flag',
			'type' => self::TYPE_INTEGER
		),
		'bet_freebet_amount' => array(
			'name' => 'Free Bet Amount',
			'type' => self::TYPE_FLOAT
		),
		'boxed_flag' => array(
				'name' => 'Boxed Flag',
				'type' => self::TYPE_INTEGER
		),
		'combinations' => array(
				'name' => 'Combinations',
				'type' => self::TYPE_INTEGER
		),
		'percentage' => array(
				'name' => 'Percentage',
				'type' => self::TYPE_FLOAT
		),
		'selection_string' => array(
				'name' => 'Selection String',
				'type' => self::TYPE_STRING
		),
			
	);

	/**
	 * Get a single bet record by bet id.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBet($id)
	{
		return $this->load($id);
	}

	/**
	 * Get a single bet record by External Bet ID.
	 *
	 * @param integer $ext_bet_id
	 * @return object
	 */
	public function getEventByExternalEventGroupIDAndExternalEventID($ext_bet_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('external_bet_id', $ext_bet_id),
		), 	SuperModel::FINDER_SINGLE);
	}


	/**
	 * Get bet records by user id.
	 *
	 * @param integer $user_id
	 * @return array
	 */
	public function getBetListByUserID($user_id)
	{
		return $this->find(array(SuperModel::getFinderCriteria('user_id', $user_id)));
	}

	/**
	 * Get bet records by user id and event id
	 *
	 * @param integer $user_id
	 * @param integer $event_id
	 * @return array
	 */
	public function getBetListByUserIDAndEventID($user_id, $event_id)
	{
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
	      		b.*,
	      		bt.name AS bet_type,
	      		rs.name AS bet_result_status,
	      		t.amount AS win_amount,
	      		t1.amount AS refund_amount
			FROM
				' . $db->nameQuote('#__bet') . ' AS b
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				' . $db->nameQuote('#__bet_result_status') . ' AS rs
			ON
				b.bet_result_status_id = rs.id
			INNER JOIN
				' . $db->nameQuote('#__bet_selection') . ' AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				m.id = s.market_id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS t
			ON
				t.id = b.result_transaction_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS t1
			ON
				t1.id = b.refund_transaction_id
			WHERE
				b.user_id = ' . $db->quote($user_id) . '
			AND
				e.id = ' . $db->quote($event_id) . '
			GROUP BY
				b.id
			';
	
	    $db->setQuery($query);
	    return $db->loadObjectList();
	}
	
	/**
	 * Check if a bet is a boxed bet
	 *
	 * @param integer $id
	 * @return bool
	 */
	public function isBoxedBet($id)
	{
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
				count(id)
			FROM
				' . $db->nameQuote('#__bet_selection') . '
			WHERE
				bet_id = ' . $db->quote($id) . '
			AND
				position != 0
			';
		$db->setQuery($query);
		return $db->loadResult() == 0 ;
		
	}
	
	/**
	 * Get active bet records by user id
	 *
	 * @param integer $user_id
	 * @return array
	 */
	public function getActiveBetListByUserID($user_id)
	{
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
	      		b.*,
	      		bt.name AS bet_type,
	      		rs.name AS bet_result_status,
	      		e.id AS event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		s.name AS selection_name,
	      		s.number AS selection_number,
	      		bat.amount AS bet_total
			FROM
				' . $db->nameQuote('#__bet') . ' AS b
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				' . $db->nameQuote('#__bet_result_status') . ' AS rs
			ON
				b.bet_result_status_id = rs.id
			INNER JOIN
				' . $db->nameQuote('#__bet_selection') . ' AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				m.id = s.market_id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS bat
			ON
				bat.id = b.bet_transaction_id
			WHERE
				b.user_id = ' . $db->quote($user_id) . '
			AND
				b.resulted_flag = 0
			
			GROUP BY
				b.id
			';
	
	    $db->setQuery($query);
	    return $db->loadObjectList();
	}
	
	/**
	 * Get recent bet records by user id
	 *
	 * @param integer $user_id
	 * @return array
	 */
	public function getBetRecentListByUserID($user_id, $from_time = false, $end_time = false, $resulted_flag = null, $order = false)
	{
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
	      		b.*,
	      		bt.name AS bet_type,
	      		rs.name AS bet_result_status,
	      		e.id AS event_id,
	      		e.external_event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		s.name AS selection_name,
	      		s.external_selection_id,
	      		bat.amount AS bet_total,
	      		rat.amount AS win_amount,
	      		fat.amount AS refund_amount
			FROM
				' . $db->nameQuote('#__bet') . ' AS b
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				' . $db->nameQuote('#__bet_result_status') . ' AS rs
			ON
				b.bet_result_status_id = rs.id
			INNER JOIN
				' . $db->nameQuote('#__bet_selection') . ' AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				m.id = s.market_id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS bat
			ON
				bat.id = b.bet_transaction_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS rat
			ON
				rat.id = b.result_transaction_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS fat
			ON
				fat.id = b.refund_transaction_id
			WHERE
				b.user_id = ' . $db->quote($user_id);
		
		if ($from_time) {
			$query .= ' AND e.start_date >= FROM_UNIXTIME(' . $db->quote($from_time) . ')';
		}
		
		if ($end_time) {
			$query .= ' AND e.start_date <= FROM_UNIXTIME(' . $db->quote($end_time) . ')';
		}
		
		if(!is_null($resulted_flag)) {
			$query.= ' AND b.resulted_flag = ' . $db->quote($resulted_flag);
		}
		
		$query .= '
			GROUP BY
				b.id
			';

		if($order) {
			$query .= ' ORDER BY ' . $order;
		} else {
			$query .= ' ORDER BY e.start_date ASC, b.created_date DESC';
		}
		
	    $db->setQuery($query, 0, 15);
	    return $db->loadObjectList();
	}
	
	/**
	 * Get fliltered bet records
	 *
	 * @param array $filter
	 * @return array
	 */
	public function getBetFilterList($filter=array(), $order=null, $direction='ASC', $limit=null, $offset=null)
	{
		$keyword		= isset($filter['keyword']) ? $filter['keyword'] : null;
		$result_type	= isset($filter['result_type']) ? $filter['result_type'] : null;
		$from_time		= isset($filter['from_time']) ? $filter['from_time'] : null;
		$to_time		= isset($filter['to_time']) ? $filter['to_time'] : null;
		$from_amount	= isset($filter['from_amount']) ? $filter['from_amount'] : null;
		$to_amount		= isset($filter['to_amount']) ? $filter['to_amount'] : null;
		$user_id		= isset($filter['user_id']) ? $filter['user_id'] : null;
		
		if (is_null($order)) {
			$order = 'b.id';
		}
		
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
	      		b.*,
	      		u.username,
	      		bt.name AS bet_type,
	      		rs.name AS bet_result_status,
	      		e.id AS event_id,
	      		e.external_event_id,
	      		e.name AS event_name,
	      		e.number AS event_number,
	      		e.trifecta_dividend,
	      		e.firstfour_dividend,
	      		e.quinella_dividend,
	      		e.exacta_dividend,
	      		s.name AS selection_name,
	      		bs.selection_id,
	      		s.external_selection_id,
	      		bat.amount AS bet_total,
	      		rat.amount AS win_amount,
	      		fat.amount AS refund_amount
			FROM
				' . $db->nameQuote('#__bet') . ' AS b
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				' . $db->nameQuote('#__bet_result_status') . ' AS rs
			ON
				rs.id = b.bet_result_status_id
			INNER JOIN
				' . $db->nameQuote('#__users') . ' AS u
			ON
				u.id = b.user_id
			INNER JOIN
				' . $db->nameQuote('#__bet_selection') . ' AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				m.id = s.market_id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS bat
			ON
				bat.id = b.bet_transaction_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS rat
			ON
				rat.id = b.result_transaction_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS fat
			ON
				fat.id = b.refund_transaction_id
			';
		$where = array();
		
		if ($keyword) {
			$or_cond	= array();
			$key_word	= $db->getEscaped($keyword);
			$or_cond[]	= 'u.username LIKE "%' . $keyword . '%"';
			$or_cond[]	= 'e.name LIKE "%' . $keyword . '%"';
			$or_cond[]	= 's.name LIKE "%' . $keyword . '%"';
			
			if (ctype_digit($keyword)) {
				$or_cond[]	= 'b.id = ' . $keyword;
				$or_cond[]	= 'b.external_bet_id = ' . $keyword;
			}
			
			$where[] = '(' . implode(' OR ', $or_cond) . ')';
		}
		
		if ($user_id) {
			$where[] = 'b.user_id = ' . $db->quote($user_id);
		}
		
		switch ($result_type) {
			case 'unresulted':
				$where[] = 'b.resulted_flag = 0';
				$where[] = 'b.refunded_flag = 0';
				break;
			case 'winning':
				$where[] = 'b.result_transaction_id IS NOT NULL';
				$where[] = 'b.result_transaction_id > 0';
				$where[] = 'b.resulted_flag = 1';
				$where[] = 'rat.amount > 0';
				break;
			case 'losing':
				$where[] = '(b.result_transaction_id IS NULL OR b.result_transaction_id=0 OR rat.amount = 0)';
				$where[] = 'b.resulted_flag = 1';
				$where[] = 'b.refund_transaction_id IS NULL';
				break;
			case 'refunded':
				$where[] = 'b.refunded_flag = 1';
				$where[] = '(b.result_transaction_id IS NULL OR b.result_transaction_id = 0 OR rat.amount = 0)';
				break;
		}
		
		if ($from_time) {
			$where[] = 'b.created_date >= FROM_UNIXTIME(' . $db->quote($from_time) . ')';
		}
		
		if ($to_time) {
			$where[] = 'b.created_date < FROM_UNIXTIME(' . $db->quote($to_time) . ')';
		}
		
		if (ctype_digit($from_amount)) {
			$where[] = 'abs(bat.amount) >=' . $from_amount * 100;
		}
		
		if (ctype_digit($to_amount)) {
			$where[] = 'abs(bat.amount) <=' . $to_amount * 100;
		}
		
		if (count($where) > 0) {
			$query .='
				WHERE
			';
			$query .= implode(' AND ', $where);
		}
		
		$query .= '
			GROUP BY
				b.id
			';

		$query .= ' ORDER BY ' . $order;

	    $db->setQuery($query, $offset, $limit);
	    return $db->loadObjectList();
	}
	
	
	/**
	 * Count the number of fliltered bet records
	 *
	 * @param array $filter
	 * @return array
	 */
	public function getBetFilterCount($filter=array())
	{
		$keyword		= isset($filter['keyword']) ? $filter['keyword'] : null;
		$result_type	= isset($filter['result_type']) ? $filter['result_type'] : null;
		$from_time		= isset($filter['from_time']) ? $filter['from_time'] : null;
		$to_time		= isset($filter['to_time']) ? $filter['to_time'] : null;
		$from_amount	= isset($filter['from_amount']) ? $filter['from_amount'] : null;
		$to_amount		= isset($filter['to_amount']) ? $filter['to_amount'] : null;
		$user_id		= isset($filter['user_id']) ? $filter['user_id'] : null;
		
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
				COUNT(b.id)
			FROM
				' . $db->nameQuote('#__bet') . ' AS b
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				' . $db->nameQuote('#__bet_result_status') . ' AS rs
			ON
				rs.id = b.bet_result_status_id
			INNER JOIN
				' . $db->nameQuote('#__users') . ' AS u
			ON
				u.id = b.user_id
			LEFT JOIN
				' . $db->nameQuote('#__bet_selection') . ' AS bs
			ON 
				b.id = bs.bet_id
			LEFT JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				m.id = s.market_id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
			ON
				e.id = m.event_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS bat
			ON
				bat.id = b.bet_transaction_id
			LEFT JOIN
				' . $db->nameQuote('#__account_transaction') . ' AS rat
			ON
				rat.id = b.result_transaction_id
			';
		$where = array();
		
		if ($keyword) {
			$or_cond	= array();
			$key_word	= $db->getEscaped($keyword);
			$or_cond[]	= 'u.username LIKE "%' . $keyword . '%"';
			$or_cond[]	= 'e.name LIKE "%' . $keyword . '%"';
			$or_cond[]	= 's.name LIKE "%' . $keyword . '%"';
			
			if (ctype_digit($keyword)) {
				$or_cond[]	= 'b.id = ' . $keyword;
				$or_cond[]	= 'b.external_bet_id = ' . $keyword;
			}
			
			$where[] = '(' . implode(' OR ', $or_cond) . ')';
		}
		
		if ($user_id) {
			$where[] = 'b.user_id = ' . $db->quote($user_id);
		}
		
		switch ($result_type) {
			case 'unresulted':
				$where[] = 'b.resulted_flag = 0';
				$where[] = 'b.refunded_flag = 0';
				break;
			case 'winning':
				$where[] = 'b.result_transaction_id IS NOT NULL';
				$where[] = 'b.result_transaction_id > 0';
				$where[] = 'b.resulted_flag = 1';
				break;
			case 'losing':
				$where[] = '(b.result_transaction_id IS NULL OR b.result_transaction_id=0)';
				$where[] = 'b.resulted_flag = 1';
				break;
			case 'refunded':
				$where[] = 'b.refunded_flag = 1';
				$where[] = '(b.result_transaction_id IS NULL OR b.result_transaction_id = 0)';
				break;
		}
		
		if ($from_time) {
			$where[] = 'b.created_date >= FROM_UNIXTIME(' . $db->quote($from_time) . ')';
		}
		
		if ($to_time) {
			$where[] = 'b.created_date < FROM_UNIXTIME(' . $db->quote($to_time) . ')';
		}
		
		if (ctype_digit($from_amount)) {
			$where[] = 'abs(bat.amount) >=' . $from_amount * 100;
		}
		
		if (ctype_digit($to_amount)) {
			$where[] = 'abs(bat.amount) <=' . $to_amount * 100;
		}
		
		if (count($where) > 0) {
			$query .= '
				WHERE
			';
			$query .= implode(' AND ', $where);
		}
		
		$query .= '
			GROUP BY
				b.id
		';
		
	    $db->setQuery($query);
	    $db->loadObject();
	    $db->setQuery('SELECT FOUND_ROWS()');
	    return $db->loadResult();
	}
	
	/**
	 * Get bet details info
	 *
	 * @param $id
	 * @return object
	 */
	public function getBetDetails($id)
	{
			//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
	      		b.*,
	      		bt.name AS bet_type,
	      		rs.name AS bet_result_status,
	      		e.id AS event_id,
	      		e.name AS event_name,
	      		e.external_event_id,
	      		s.name AS selection_name,
	      		s.external_selection_id,
	      		s.number AS selection_number
			FROM
				' . $db->nameQuote('#__bet') . ' AS b
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' AS bt
			ON
				bt.id = b.bet_type_id
			INNER JOIN
				' . $db->nameQuote('#__bet_result_status') . ' AS rs
			ON
				b.bet_result_status_id = rs.id
			INNER JOIN
				' . $db->nameQuote('#__bet_selection') . ' AS bs
			ON 
				b.id = bs.bet_id
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON 
				s.id = bs.selection_id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS m
			ON
				m.id = s.market_id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
			ON
				e.id = m.event_id
			WHERE
				b.id = ' . $db->quote($id) . '
			GROUP BY
				b.id
			';
	
	    $db->setQuery($query);
	    return $db->loadObject();
	}
	
	/**
	 * Get Array of unresulted bets
	 * @return array
	 */
	public function getUnresultedBetList($only_complete_events=false, $product_id)
	{
		$db =& $this->getDBO();
		$query = 'SELECT count(b.id) bet_cnt,b.id, 
		b.external_bet_id, 
		b.user_id, 
		b.bet_amount, 
		b.bet_type_id, 
		b.bet_result_status_id, 
		b.bet_origin_id, 
		b.bet_product_id, 
		b.bet_transaction_id, 
		b.bet_freebet_transaction_id,		
		b.result_transaction_id, 
		b.refund_transaction_id, 
		b.refund_freebet_transaction_id,		
		b.resulted_flag, 
		b.refunded_flag, 
		b.flexi_flag, 
		b.fixed_odds, 
		b.created_date, 
		b.updated_date,
		b.invoice_id,
		b.bet_freebet_flag,
		b.bet_freebet_amount 
		FROM 
			'. $db->nameQuote('#__bet'). ' AS b 
		INNER JOIN 
			' .$db->nameQuote('#__bet_result_status'). ' AS brs 
		ON 
			b.bet_result_status_id = brs.id 
		INNER JOIN 
			'. $db->nameQuote('#__bet_selection'). ' AS bs 
		ON 
			b.id = bs.bet_id 
		INNER JOIN
		 	'. $db->nameQuote('#__selection').' AS s 
		ON 
			bs.selection_id = s.id 
		INNER JOIN 
			'. $db->nameQuote('#__market').' AS m 
		ON 
			s.market_id = m.id 
		INNER JOIN 
			'. $db->nameQuote('#__event').' AS e 
		ON 
			m.event_id = e.id 
		WHERE 
			brs.name in("'.BettingModelBetResultStatus::STATUS_UNRESULTED.'") 
		AND 
			b.bet_product_id=' . $db->quote($product_id) . '
		AND 
			e.start_date < NOW() AND b.external_bet_id>0 group by b.id
		'; 
		
		$db->setQuery($query);
		
		return $this->_loadModelList($db->loadObjectList('external_bet_id'));
	}
	
	/**
	 * Get Array of unresulted bets
	 * @return array
	 */
	public function getPendingBetList($only_complete_events=false, $product_id)
	{
		$db =& $this->getDBO();
		$query = 'SELECT b.id, 
		b.external_bet_id, 
		b.user_id, 
		b.bet_amount, 
		b.bet_type_id, 
		b.bet_result_status_id, 
		b.bet_origin_id, 
		b.bet_product_id, 
		b.bet_transaction_id,
		b.bet_freebet_transaction_id,
		b.result_transaction_id, 
		b.refund_transaction_id,
		b.refund_freebet_transaction_id,
		b.resulted_flag, 
		b.refunded_flag, 
		b.flexi_flag, 
		b.fixed_odds, 
		b.created_date, 
		b.updated_date,
		b.bet_freebet_flag,
		b.bet_freebet_amount 
		FROM 
			'. $db->nameQuote('#__bet'). ' AS b 
		INNER JOIN 
			' .$db->nameQuote('#__bet_result_status'). ' AS brs 
		ON 
			b.bet_result_status_id = brs.id 
		WHERE 
			brs.name in("'.BettingModelBetResultStatus::STATUS_PENDING.'") 
		AND 
			b.bet_product_id=' . $db->quote($product_id) . '
		AND 
			b.external_bet_id>0 
		'; 
		
		$db->setQuery($query);
		
		return $this->_loadModelList($db->loadObjectList('external_bet_id'));
	}
	
	/**
	 * Get the last bet timestamp by user id
	 * @return int
	 */
	public function getLastBetTimeStampByUserID($user_id)
	{
		$db =& $this->getDBO();
		$query = '
			SELECT
				UNIX_TIMESTAMP(`created_date`)
			FROM
				' . $db->nameQuote('#__bet') . '
			WHERE
				user_id = ' . $db->quote($user_id) . '
			ORDER BY
				created_date DESC
			LIMIT 1
		';
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Get the last bet timestamp by user id for Api
	 * @return int
	 */
	public function getLastBetTimeStampByUserIDApi($user_id)
	{
		$db =& $this->getDBO();
		$query = '
			SELECT
				UNIX_TIMESTAMP(`created_date`) AS created_date
			FROM
				' . $db->nameQuote('#__bet') . '
			WHERE
				user_id = ' . $db->quote($user_id) . '
			ORDER BY
				created_date DESC
			LIMIT 1
		';
		$db->setQuery($query);
		return $db->loadObject();
	}
}
