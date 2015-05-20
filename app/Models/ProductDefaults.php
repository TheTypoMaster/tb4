<?php namespace TopBetta\Models;

class ProductDefaults extends \Eloquent {
	protected $table = 'tb_product_default';
    protected $guarded = array();

    public static $rules = array();


    public static function getTotePaidForMeeting($meetingCountry, $meetingGrade, $meetingType){

        return ProductDefaults::join('tbdb_bet_product', 'tbdb_bet_product.id', '=', 'tb_product_default.tb_product_id')
                                ->join('tb_product_provider_match', 'tb_product_provider_match.tb_product_id', '=', 'tbdb_bet_product.id')
                                ->where('tb_product_default.country', $meetingCountry)
                                ->where('tb_product_default.region', $meetingGrade)
                                ->where('tb_product_default.type_code', $meetingType)
                                ->get();

        }


}