<?php namespace TopBetta;

class BetProduct extends \Eloquent {
	
	protected $table = 'tbdb_bet_product';
    protected $guarded = array();

    public static $rules = array();
    
    public static function isProductUsed($priceType, $betType, $meetingCountry, $meetingGrade, $meetingTypeCode, $providerName){

    	
    	$productID = "select bp.id 
    				from tbdb_bet_product as bp 
    				left join tb_product_default as pd on pd.tb_product_id = bp.id
    				left join tb_product_provider_match as ppm on ppm.tb_product_id = bp.id 
    				left join tb_product_provider as tpp on tpp.id = ppm.provider_id 
    				where provider_product_name = '$priceType' 
    				and `bet_type` = '$betType' 
    				and `type_code` = '$meetingTypeCode' 
    				and `country` = '$meetingCountry' 
    				and `region` = '$meetingGrade' 
    				and `provider_name` = '$providerName'
    				limit 1";
    	
    	$result = \DB::select($productID);
    	
    	//TODO: should be using the below eloquent
    	/* $productID = BetProduct::leftjoin('tb_product_default AS pd', 'pd.tb_product_id', '=', 'tbdb_bet_product.id')
    										->leftjoin('tb_product_provider_match AS ppm', 'ppm.tb_product_id', '=', 'tbdb_bet_product.id')
    										->leftjoin('tb_product_provider AS tpp', 'tpp.id', '=', 'ppm.provider_id')
    										->where('provider_product_name', '=', $priceType)
    										->where('bet_type', '=', $betType)
    										->where('type_code', '=', $meetingTypeCode)
    										->where('country', '=', $meetingCountry)
    										->where('region', '=', $meetingGrade)
    										->where('provider_name', '=', $providerName)
    										->pluck('tbdb_bet_product.id'); */
    	return $result;
	}
}