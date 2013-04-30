<?php
class RaceEvent extends Eloquent {

	protected $table = 'tbdb_event';
	
	
	public function meetings(){
		return $this->hasMany('RaceSelections', '');
	}
	

}