<?php
defined('_JEXEC') or die();

jimport('mobileactive.wagering.bet.selection');
jimport('mobileactive.wagering.bet.validator');

interface iBet{
	
	const BET_TYPE_WIN 		= 'win',
		BET_TYPE_PLACE 		= 'place',
	 	BET_TYPE_EACHWAY 	= 'eachway',
	 	BET_TYPE_TRIFECTA 	= 'trifecta',
	 	BET_TYPE_QUINELLA 	= 'quinella',
 		BET_TYPE_FIRSTFOUR 	= 'firstfour',
	 	BET_TYPE_EXACTA 	= 'exacta';
	
	public function getTotalBetAmount();
	public function addSelection($id, $position = null);
	public function isValid();
	public function getErrorMessage();
}

class WageringBet implements iBet{
	/**
	 * Current Bet Types
	 * @var string
	 */
	private $bet_type = null;
	/**
	 * Standard Bet Types
	 * @staticvar array
	 */
	public static $standard_bet_type_list = array(
		self::BET_TYPE_WIN, 
		self::BET_TYPE_PLACE, 
		self::BET_TYPE_EACHWAY
	);
	
	private $bet_type_display_name = array(
		self::BET_TYPE_WIN		=> 'win',
		self::BET_TYPE_PLACE 		=> 'place',
	 	self::BET_TYPE_EACHWAY	=> 'each way',
	 	self::BET_TYPE_TRIFECTA	=> 'trifecta',
	 	self::BET_TYPE_QUINELLA	=> 'quinella',
 		self::BET_TYPE_FIRSTFOUR	=> 'first four',
	 	self::BET_TYPE_EXACTA		=> 'exacta',
	);
	/**
	 * Maximum selection count
	 * @var integer
	 */
	protected $maximum_selection_count = 1;
	/**
	 * Postion selection count
	 * @var integer
	 */
	protected $position_selection_count = 1;
	/**
	 * Flexi bet type
	 * @var boolean
	 */
	protected $flexi_bet_type = false;
	/**
	 * Combination bet
	 * @var boolean
	 */
	protected $combination_bet_type = false;
	/**
	 * List of current selections
	 * @var object
	 */
	protected $bet_selection_object  = null;
	
	/**
	 * Bet data
	 * @var array
	 */
	private $data = array(
		'flexi_flag' => false,
		'amount' => 0,
		'odds' => 0,
		'fixed_odds' => 0,
		'boxed_flag' => false,
		'race_number' => 0 );
	/* *
	 * Bet error message
	 * @var string
	 */
	private $error_message = '';
	
	public static function newBet($type='win', $bet_amount = 0, $boxed_flag = false, $flexi_flag = false, $race_number = 0)
	{
		if (in_array($type, self::$standard_bet_type_list)){
			$bet = new WageringBet($type);
		}
		else {
			jimport('mobileactive.wagering.bet.exotic.'.$type);
			$class = 'WageringBetExotic'.$type;
			if (class_exists($class)){
				$bet = new $class($type);
			} else {
				throw('Bet Type class does not exist');
			}
		}
		
		$bet->amount = $bet_amount;
		$bet->boxed_flag = $boxed_flag;
		$bet->flexi_flag = $flexi_flag;
		$bet->race_number = $race_number;
		
		return $bet;
	}
	
	final public function __construct($type)
	{
		$self = get_class($this);
		
		if ($self == 'Bet' && !in_array($type, self::$standard_bet_type_list)){
			throw new Exception('Non standard bet type');
		}
		
		$this->bet_type = $type;
	}
	
	final public function __get($key)
	{
		if (array_key_exists($key, $this->data)){
			return $this->data[$key];
		}
		else{
			throw new Exception('Entity ('.$key.') does not exist in allowed data array.');
		}	
	}
	
	final public function __set($key, $var)
	{
		if (array_key_exists($key, $this->data)){
			$this->data[$key] = $var;
		}
		else {
			throw new Exception('Entity ('.$key.') does not exist in allowed data array.');
		}
	}

	final public function addSelection($selection_number, $position = null)
	{
		if ($this->boxed_flag && !is_null($position)){
			throw new Exception('Selection position must be null if boxed_flag is set');
		}

		if (in_array($this->bet_type, self::$standard_bet_type_list) &&  !is_null($position)){
			throw new Exception('Selection position must be null if standard bet type');
		}
		
		$selection = $this->getBetSelectionObject();
		$s = print_r($selection,true);
		file_put_contents('/tmp/saveExoticsBet', "* ADD Selection. Object:". $s. "\n", FILE_APPEND | LOCK_EX);
		
		if (is_null($position)){
			$selection->add($selection_number);	
		} else {
			$selection->add($selection_number, $position);
		}
		
		return $this;
	}
	
	final public function getBetSelectionObject(){
		if(is_null($this->bet_selection_object)){
			$this->bet_selection_object = new WageringBetSelection($this);
		}
		
		return $this->bet_selection_object;
	}
	
	final public function isValid()
	{
		try{
			$validator = new WageringBetValidator($this);
		}
		catch(ValidatorException $e){
			$this->_setErrorMessage($e->getMessage());
			return false;
		}
		return true;
	}
	
	final public function getErrorMessage()
	{
		return $this->error_message;
	}
	
	final private function _setErrorMessage($error_message)
	{
		$this->error_message = $error_message;
	}
	
	public function getBetAmount()
	{
		return $this->amount;	
	}
	
	public function getBetType()
	{
		if($this->bet_type == 'firstfour'){
			return 'FirstFour';
		}
		return ucfirst($this->bet_type);
	}
	
	public function getTotalBetAmount()
	{
		if ($this->combination_bet_type){
			if (!$this->flexi_flag){
				if (method_exists($this, 'getCombinationCount')){
					return $this->getCombinationCount() * $this->amount;
				}
				else {
					throw('Method getCombinationCount not defined');
				}
			}
		}	
		
		if ($this->bet_type == self::BET_TYPE_EACHWAY) {
			return $this->amount * 2;
		}
		return $this->amount;
	}
	
	public function isStandardBetType($bet_type = null)
	{
		if (is_null($bet_type)) {
			$bet_type = $this->bet_type;
		}
		return in_array($bet_type, self::$standard_bet_type_list);
	}
	
	public function isCombinationBetType()
	{
		return $this->combination_bet_type;	
	}
	
	public function isFlexiBetType()
	{
		return $this->flexi_bet_type;
	}
	
	public function isFlexiBet()
	{
		return $this->flexi_flag;
	}
	
	public function isBoxed()
	{
		return $this->boxed_flag;	
	}
	
	public function getPositionSelectionCount()
	{
		return $this->position_selection_count;	
	}
	
	public function getMaximumSelectionCount()
	{
		return $this->maximum_selection_count;	
	}
	
	public function getBetTypeDisplayName($bet_type = null, $capitalised = true)
	{
		if (is_null($bet_type)) {
			$bet_type = $this->bet_type;
		}
		
		$bet_type_name = $this->bet_type_display_name[$bet_type];
		
		if ($capitalised) {
			$bet_type_name = strtoupper($bet_type_name);
		}
		
		return $bet_type_name;
	}

	public function getBetSelectionList()
	{
		$selection = $this->getBetSelectionObject();
		return $selection->getList();
	}
	
	public function displayBetSelections()
	{
		$bet_display	= '';
		$selection_list	= $this->getBetSelectionList();
		
		switch ($this->bet_type) {
			case self::BET_TYPE_WIN: 
			case self::BET_TYPE_PLACE:
			case self::BET_TYPE_EACHWAY:
				$bet_display = $selection_list[0];
			break;
			case self::BET_TYPE_QUINELLA:
			case self::BET_TYPE_EXACTA:
			case self::BET_TYPE_TRIFECTA:
			case self::BET_TYPE_FIRSTFOUR:
				if ($this->isBoxed()) {
					$bet_display = implode(', ', $selection_list) . ' (BOXED)';
				} else {
					$exotic_display = array();
					foreach ($selection_list as $selections) {
						$exotic_display[] = implode(', ', $selections);
					}
					
					$bet_display = implode(' / ', $exotic_display);
				}
			break;
		}
		
		return $bet_display;
	}

	public function formatBetSelections()
	{
		$bet_display	= '';
		$selection_list	= $this->getBetSelectionList();
		
		switch ($this->bet_type) {
			case self::BET_TYPE_WIN: 
			case self::BET_TYPE_PLACE:
			case self::BET_TYPE_EACHWAY:
				$bet_display = $selection_list[0];
			break;
			case self::BET_TYPE_QUINELLA:
			case self::BET_TYPE_EXACTA:
			case self::BET_TYPE_TRIFECTA:
			case self::BET_TYPE_FIRSTFOUR:
				if ($this->isBoxed()) {
					$bet_display = implode(',', $selection_list);
				} else {
					$exotic_display = array();
					foreach ($selection_list as $selections) {
						$exotic_display[] = implode(',', $selections);
					}
					
					$bet_display = implode(':', $exotic_display);
				}
			break;
		}
		
		return $bet_display;
	}
}
