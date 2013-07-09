<?php namespace TopBetta;

class DataValues extends Eloquent {
	protected $table = 'tbdb_data_values';
    protected $guarded = array();

    public static $rules = array();
    
    
    /**
     * Get System Value.
     * @param $type
     * @param $value
     * @return string
     * - The value 
     */
    static public function getDefaultValue($type, $value) {
    	
    	$valueName = "select dv.value
	    	from tb_data_values as dv
	    	left join tb_data_types as dt on dt.id = dv.data_type_id
	    	left join tb_data_provider_match as dpm on dpm.data_value_id = dv.id
	    	left join tb_data_provider as dp on dp.id = dpm.provider_id
	    	where dpm.value = '$value'
	    	and dt.data_type = '$type'
	       	limit 1";
	    	 
    	$result = \DB::select($valueName);
    	
    	return $result;
    }
    
}