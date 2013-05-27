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
     * String ID for pending bets
     *
     * @var string
     */
    const STATUS_PENDING = 'pending';
    
}