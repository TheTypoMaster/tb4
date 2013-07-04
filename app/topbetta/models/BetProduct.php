<?php namespace TopBetta;

class BetProduct extends \Eloquent {
	
	protected $table = 'tbdb_bet_product';
    protected $guarded = array();

    public static $rules = array();
    
    public static function isProductUsed($priceType, $betType, $meetingCountry, $meetingGrade, $meetingTypeCode, $providerName){
    	 
    	return	BetProduct::join('tb_product_default AS pd', 'pd.id', '=', 'tbdb_bet_product.id')
    										->join('tb_product_provider_match AS ppm', 'ppm.tb_product_id', '=', 'pd.id')
    										->join('tb_product_provider AS pp', 'pp.id ', '=', 'ppm.provider_id')
    										->where('provider_product_name', '=', $priceType)
    										->where('bet_type', '=', $betType)
    										->where('type_code', '=', $meetingTypeCode)
    										->where('country', '=', $meetingCountry)
    										->where('region', '=', $meetingGrade)
    										->where('provider_name', '=', $providerName)
    										->pluck('tbdb_bet_product.id');
    
    }
}