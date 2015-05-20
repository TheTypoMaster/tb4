<?php namespace TopBetta;

class BetResultStatus extends \Eloquent {
	
	protected $table = 'tbdb_bet_result_status';
    protected $guarded = array();

    public static $rules = array();
    
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
     * String ID for cancelled
     *
     * @var string
     */
    const STATUS_CANCELLED = 'cancelled';

    /**
     * String ID for pending bets
     *
     * @var string
     */
    const STATUS_PENDING = 'pending';
    
    /**
     * get bet result ID for status
     * @param $result_status
     * @return int
     * - text of the result status
     */
    static public function getBetResultStatusByName($result_status) {
    	return BetResultStatus::where('name', '=', $result_status) -> pluck('id');
    }
    
    
}