<?php
/**
 * Coded by Oliver Shanahan
 * File creation date: 29/09/2014
 * File creation time: 3:36 PM
 * Project: tb4
 */

/**
 * Sports model config
 */

return array(

    'title' => 'Competitions',

    'single' => 'competition',

    'model' => '\TopBetta\Models\SportsComps',

    /**
     * The display columns
     */
    'columns' => array(
        'id',
        'name' => array(
            'title' => 'Competition Name'
        ),
        'sport_name' => array(
            'title' => 'Sport Name',
            'relationship' => 'sports',
            'select' => '(:table).name',
        ),
        'start_date' => array(
            'title' => 'Start Date'
        ),
        'close_time' => array(
            'title' => 'End Date'
        ),
        'display_flag' => array(
            'title' => 'Display on TopBetta'
        ),
    ),

    'edit_fields' => array(
        'name' => array(
            'title' => 'Competition Name',
            'type' => 'text'
        ),
        'sports' => array(
            'title' => 'Sport Name',
            'type' => 'relationship'
        ),
        'start_date' => array(
            'title' => 'Start Date',
            'type' => 'datetime',
            'date_format' => 'yy-mm-dd',
            'time_format' => 'HH:mm',
        ),
        'close_time' => array(
            'title' => 'Close Date',
            'type' => 'datetime',
            'date_format' => 'yy-mm-dd',
            'time_format' => 'HH:mm',
        ),
        'country' => array(
            'title' => 'Country',
            'type' => 'text'
        ),

        'display_flag' => array(
            'title' => 'Display on TopBetta',
            'type' => 'bool'
        ),


        // non-nullable fields
        'wagering_api_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'tournament_competition_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'created_date' => array(
            'visible' => false,
            'value' => '2014-10-01 00:00:00'
        ),
        'updated_date' => array(
            'visible' => false,
            'value' => '2014-10-01 00:00:00'
        ),
        'meeting_grade' => array(
            'visible' => false,
            'value' => '0'
        ),
        'rail_position' => array(
            'visible' => false,
            'value' => '0'
        ),
    ),

    /**
     * The sort options for a model
     *
     * @type array
     */
    'sort' => array(
        'field' => 'start_date',
        'direction' => 'desc',
    ),

    /**
     * The validation rules for the form, based on the Laravel validation class
     *
     * @type array
     */
    'rules' => array(
        'name' => 'required|max:64',
        'sport_id' => 'required|integer',
        'country' => 'required',
        'start_date' => 'required',
        'close_time' => 'required',
    ),


    /**
     * The filterable fields
     *
     * @type array
     */
    'filters' => array(

        'name' => array(
            'title' => 'Competition Name',
            'type' => 'text'
        ),

        'sports' => array(
            'title' => 'Sport Name',
            'type' => 'relationship'
        ),

        'start_date' => array(
            'title' => 'Start Date',
            'type' => 'datetime',
            'date_format' => 'yy-mm-dd',
            'time_format' => 'HH:mm',
        ),

        'close_time' => array(
            'title' => 'End Date',
            'type' => 'datetime',
            'date_format' => 'yy-mm-dd',
            'time_format' => 'HH:mm',
        ),

        'display_flag' => array(
            'title' => 'Display on TopBetta',
            'type' => 'bool'
        ),
    ),
);