<?php
namespace TopBetta;

class TopBettaUser extends \Eloquent {
    
	protected $table = 'tbdb_topbetta_user';	
		
    protected $guarded = array();

    public static $rules = array();
}