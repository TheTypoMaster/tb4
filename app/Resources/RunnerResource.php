<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 3:06 PM
 */

namespace TopBetta\Resources;


class RunnerResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\RunnerModel';

    protected $attributes = array(
        "id"        => "id",
        "name"      =>'name',
        "owner"     => 'owner.name',
        "trainer"   => 'trainer.name',
        "colour"    =>'colour',
        "sex"       =>'sex',
        "foal_date" =>'foal_date',
        "sire"      =>'sire',
        "dam"       =>'dam',
        'lastStarts' => 'lastStarts',
    );

    private $form = array();

    private $lastStarts = array();

    public function toArray()
    {
        $array = parent::toArray();

        return array_merge($array, $this->getForm());
    }

//    public static function createResourceFromArray($array, $class = null)
//    {
//        parent::createResourceFromArray($array);
//    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        if (!count($this->form)) {
            return $this->getFormFromModel();
        }

        return $this->form;
    }
    
    public function getFormFromModel()
    {
        return array(
            'age'            => $this->model->age,
            'colour'         => $this->model->colour,
            'sex'            => $this->model->sex,
            'career'         => $this->model->career,
            'distance'       => $this->model->distance,
            'track'          => $this->model->track,
            'track_distance' => $this->model->track_distanc,
            'first_up'       => $this->model->first_up,
            'second_up'      => $this->model->second_up,
            'good'           => $this->model->good,
            'firm'           => $this->model->firm_,
            'soft'           => $this->model->soft,
            'synthetic'      => $this->model->synthetic,
            'wet'            => $this->model->wet,
            'nonwet'         => $this->model->nonwet,
            'night'          => $this->model->night,
            'jumps'          => $this->model->jumps,
            'season'         => $this->model->season,
            'heavy'          => $this->model->heavy,
        );
    }

    /**
     * @param $runnersForm
     */
    public function setForm($runnersForm)
    {
        $this->form = array(
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

    /**
     * @return mixed
     */
    public function getLastStarts()
    {
        return count($this->lastStarts) ? $this->lastStarts : array_get($this->model, 'lastStarts', array());
    }

    /**
     * @param mixed $lastStarts
     */
    public function setLastStarts($lastStarts)
    {
        $this->lastStarts = array();

        foreach($lastStarts as $start) {
            $this->lastStarts[] = array(
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
    }
}