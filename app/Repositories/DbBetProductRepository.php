<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 22:27
 * Project: tb4
 */

use TopBetta\Models\BetProductModel;;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;

class DbBetProductRepository extends BaseEloquentRepository implements BetProductRepositoryInterface{

    protected $betproducts;

    public function __construct(BetProductModel $betproducts)
    {
        $this->model = $betproducts;
    }

    public function isProductUsed($priceType, $betType, $meetingCountry, $meetingGrade, $meetingTypeCode, $providerName){


//        $productID = "select bp.id
//    				from tbdb_bet_product as bp
//    				left join tb_product_default as pd on pd.tb_product_id = bp.id
//    				left join tb_product_provider_match as ppm on ppm.tb_product_id = bp.id
//    				left join tb_product_provider as tpp on tpp.id = ppm.provider_id
//    				where provider_product_name = '$priceType'
//    				and `bet_type` = '$betType'
//    				and `type_code` = '$meetingTypeCode'
//    				and `country` = '$meetingCountry'
//    				and `region` = '$meetingGrade'
//    				and `provider_name` = '$providerName'
//    				limit 1";
//
//        $result = \DB::select($productID);

        return $this->model->leftjoin('tb_product_default AS pd', 'pd.tb_product_id', '=', 'tbdb_bet_product.id')
                            ->leftjoin('tb_product_provider_match AS ppm', 'ppm.tb_product_id', '=', 'tbdb_bet_product.id')
                            ->leftjoin('tb_product_provider AS tpp', 'tpp.id', '=', 'ppm.provider_id')
                            ->where('provider_product_name', '=', $priceType)
                            ->where('bet_type', '=', $betType)
                            ->where('type_code', '=', $meetingTypeCode)
                            ->where('country', '=', $meetingCountry)
                            ->where('region', '=', $meetingGrade)
                            ->where('provider_name', '=', $providerName)
                            ->first();

    }

    public function getProductByCode($productCode)
    {
        return $this->model
            ->join('tb_product_provider_match as ppm', 'ppm.tb_product_id', '=', 'tbdb_bet_product.id')
            ->where('ppm.provider_product_name', $productCode)
            ->first(array('tbdb_bet_product.*'));
    }

    public function getProductsByCodes($productCodes)
    {
        return $this->model
            ->join('tb_product_provider_match as ppm', 'ppm.tb_product_id', '=', 'tbdb_bet_product.id')
            ->whereIN('ppm.provider_product_name', $productCodes)
            ->get(array('tbdb_bet_product.*'));
    }

    public function getProductsForUser($user, $venue)
    {
        return $this->model
            ->join('tb_user_products', 'tb_user_products.bet_product_id', '=', 'tbdb_bet_product.id')
            ->join('tbdb_bet_type', 'tbdb_bet_type.id', '=', 'tb_user_products.bet_type_id')
            ->where('user_id', $user)
            ->where(function($q) use ($venue) {
                $q->where('venue_id', $venue)->orWhere('venue_id', 0);
            })
            ->orderBy('venue_id', 'DESC')
            ->get(array('tbdb_bet_product.*', 'tbdb_bet_type.name as bet_type', 'tb_user_products.venue_id as venue_id'));
    }

    public function getFixedOddsProducts()
    {
        return $this->model
            ->where('is_fixed_odds', true)
            ->get();
    }
}