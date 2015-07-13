<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 11:17 AM
 */

namespace TopBetta\Services\Resources;


use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;

class SelectionResourceService {

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepositoryInterface;

    public function __construct(SelectionRepositoryInterface $selectionRepositoryInterface)
    {
        $this->selectionRepositoryInterface = $selectionRepositoryInterface;
    }

    public static function getDefaultRelations()
    {
        return array(
            'result',
            'price',
            'runner',
            'runner.owner',
            'runner.trainer',
            'form',
            'lastStarts'
        );
    }

    public function formatForResponse($selection)
    {
        $response = array(
            'name' => $selection->name,
            'number' => $selection->number,
            'jockey' => $selection->assosciate,
            'barrier' => $selection->barrier,
            'handicap' => $selection->handicap,
            'weight' => $selection->weight,
            'win_odds' => object_get($selection, 'price.win_odds', 0),
            'place_odds' => object_get($selection, 'price.place_odds', 0),
            'silk_id' => $selection->silk_id,
        );

        if( $runner = object_get($selection, 'runner') ) {
            $response['runner'] = $this->formatRunner($runner);


            if ($lastStarts = object_get($selection, 'lastStarts')) {
                $response['runner']['last_starts'] = $this->formatLastStarts($lastStarts);
            }

            if ($form = object_get($selection, 'form')) {
                $response['runner']['detailed_form'] = $this->formatDetailedForm($form);
            }
        }

        return $response;
    }

    public function formatLastStarts($lastStarts)
    {
        $response = array();

        foreach($lastStarts as $start) {
            $response[] = array(
                'id'                   => (int)$start->id,
                'finish_position'      => (int)$start->finish_position,
                'race_starters'        => (int)$start->race_starters,
                'abr_venue'            => $start->abr_venue,
                'race_distance'        => $start->race_distance,
                'name_race_form'       => $start->name_race_form,
                'mgt_date'             => date('dM y', strtotime($start->mgt_date)),
                'track_condition'      => $start->track_condition,
                'numeric_rating'       => $start->numeric_rating,
                'jockey_initials'      => $start->jockey_initials,
                'jockey_surname'       => $start->jockey_surname,
                'handicap'             => $start->handicap,
                'barrier'              => (int)$start->barrier,
                'starting_win_price'   => $start->starting_win_price,
                'other_runner_name'    => $start->other_runner_name,
                'other_runner_barrier' => (int)$start->other_runner_barrier,
                'in_running_800'       => $start->in_running_800,
                'in_running_400'       => $start->in_running_400,
                'other_runner_time'    => trim($start->other_runner_time, '0:'),
                'margin_decimal'       => $start->margin_decimal
            );
        }

        return $response;
    }

    public function formatDetailedForm($runnersForm)
    {
        return array(
            'age'            => $runnersForm->age,
            'colour'         => $runnersForm->colour,
            'sex'            => $runnersForm->sex,
            'career'         => $runnersForm->career_results,
            'distance'       => $runnersForm->distance_results,
            'track'          => $runnersForm->track_results,
            'track_distance' => $runnersForm->track_distance_results,
            'first_up'       => $runnersForm->first_up_results,
            'second_up'      => $runnersForm->second_up_results,
            'good'           => $runnersForm->good_results,
            'firm'           => $runnersForm->firm_results,
            'soft'           => $runnersForm->soft_results,
            'synthetic'      => $runnersForm->synthetic_results,
            'wet'            => $runnersForm->wet_results,
            'nonwet'         => $runnersForm->nonwet_results,
            'night'          => $runnersForm->night_results,
            'jumps'          => $runnersForm->jumps_results,
            'season'         => $runnersForm->season_results,
            'heavy'          => $runnersForm->heavy_results
        );
    }

    public function formatRunner($runner)
    {
        return array(
            "name"      => $runner->name,
            "owner"     => object_get($runner, 'owner.name'),
            "trainer"   => object_get($runner, 'owner.trainer'),
            "colour"    => $runner->colour,
            "sex"       => $runner->sex,
            "foal_date" => $runner->foal_date,
            "sire"      => $runner->sire,
            "dam"       => $runner->dam
        );
    }
}