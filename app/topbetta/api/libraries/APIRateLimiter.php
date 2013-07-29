<?php namespace TopBetta;

use TopBetta;
/*
 * API Rate Limiter
 */

class APIRateLimiter
{
    private $rateLimitKey;
    private $rateLimitMax;
    private $rateLimitCost;
    private $rateTTL;
    private $rateLimitReset;

    function __construct($rateLimitMax, $rateLimitCost, $rateLimitKey = "rate_limiter_key", $rateTTL, $rateLimitReset = false)
    {
        $this->rateLimitMax = (float)$rateLimitMax;
        $this->rateLimitCost = (float)$rateLimitCost;
        $this->rateLimitKey	= $rateLimitKey;
        $this->rateTTL = $rateTTL;
        $this->rateLimitReset = $rateLimitReset;
    }

    /*
     *  Returns true if the rate limit is enforced
    */
    public function RateLimiter()
    {
    	// get last time stamp from cache
    	$LastCacheValue = \Cache::get($this->rateLimitKey);
    
    	// get current micro time
    	list($partMsec, $partSec) = explode(" ", microtime());
    	$currentTimeMs = $partSec.$partMsec;
    	
    	// if there is no cache entry add one
    	if($LastCacheValue === false) {
    		\Cache::add($this->rateLimitKey, $currentTimeMs, $this->rateTTL);
    		// no rate limit enforced
    		return false;
    	}
    		
      	// time since last update
    	$timeDiffMs = $currentTimeMs - $LastCacheValue;
    
       	// set the last cache value
    	if($timeDiffMs > 0) $LastCacheValue = $currentTimeMs;
    
    	// add the cost to the rate limit
    	//$LastCacheValue += $this->rateLimitCost;
    		
    	// if the time diff is less then the rate limit max
    	if($timeDiffMs > $this->rateLimitMax)
    	{
    		// update/add the cache key and value
    		if(!\Cache::put($this->rateLimitKey, $LastCacheValue, $this->rateTTL)) \Cache::add($this->rateLimitKey, $LastCacheValue, $this->rateTTL);
    			
    		// No Rate Limit enforced
    		return false;
    	}
    
    	// increase the rate limit time
    	//$LastCacheValue += ($this->rateLimitCost*2);
    
    	if ($this->rateLimitReset){
    		// update/add the cache key and value - This can be used to re-set the last cache value to current time on every connect.
    		if(!\Cache::put($this->rateLimitKey, $LastCacheValue, $this->rateTTL)) \Cache::add($this->rateLimitKey, $LastCacheValue, $this->rateTTL);
    	}
    	
    	// rate limit enforced
    	return true;
    }
    
    
}


