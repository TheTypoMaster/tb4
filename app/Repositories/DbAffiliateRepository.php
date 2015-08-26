<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 9:45 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\AffiliatesModel;
use TopBetta\Repositories\Contracts\AffiliateRepositoryInterface;

class DbAffiliateRepository extends BaseEloquentRepository implements AffiliateRepositoryInterface
{

    public function __construct(AffiliatesModel $model)
    {
        $this->model = $model;
    }

    public function getByCodeOrFail($code)
    {
        return $this->model->where('affiliate_code', $code)->firstOrFail();
    }

    public function getAffiliatesInTournamentByTypes($tournament, $types)
    {
        return $this->model
            ->from('tb_affiliates as a')
            ->join('tb_affiliate_types as at', 'at.affiliate_type_id', '=', 'a.affiliate_type_id')
            ->join('tbdb_users as u', 'u.affiliate_id', '=', 'a.affiliate_id')
            ->join('tbdb_tournament_ticket as tt', 'tt.user_id', '=', 'u.id')
            ->where('tt.tournament_id', $tournament)
            ->whereIn('at.affiliate_type_name', $types)
            ->groupBy('a.affiliate_id')
            ->get(array('a.*'));
    }
}