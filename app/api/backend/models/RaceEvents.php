<?php
class RaceEvents extends Eloquent {

	protected $table = 'tbdb_event';
	
	
	public function meetings(){
		return $this->hasMany('RaceSelections', '');
	}
	

}