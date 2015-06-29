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

    'title' => 'Events',

    'single' => 'event',

    'model' => '\TopBetta\Models\Events',

    /**
     * The display columns
     */
    'columns' => array(
        'id',

        'name' => array(
            'title' => 'Event Name'
        ),

        'competition_id' => array(
            'title' => 'Comeption Id',
            'relationship' => 'competitionpivot',
            'select' => '(:table).event_group_id'
        ),

        'event_status' => array(
            'title' => 'Event Status',
            'relationship' => 'eventstatus',
            'select' => '(:table).name'
        ),

        'start_date' => array(
            'title' => 'Start Date'
        ),

        'display_flag' => array(
            'title' => 'Display on TopBetta'
        )
    ),


    'edit_fields' => array(
        'name' => array(
            'title' => 'Event Name',
            'type' => 'text'
        ),

//        'competitions' => array(
//            'title' => 'Competiiton Name',
//            'type' => 'relationship',
//            'autocomplete' => true,
//            'num_options' => 5,
//        ),

        'start_date' => array(
            'title' => 'Start Date',
            'type' => 'datetime',
            'date_format' => 'yy-mm-dd',
            'time_format' => 'HH:mm',
        ),

        'eventstatus' => array(
            'title' => 'Event Status',
            'type' => 'relationship'
        ),

        'display_flag' => array(
            'title' => 'Display on TopBetta',
            'type' => 'bool'
        ),


        // non-nullable fields
        'created_date' => array(
            'visible' => false,
            'value' => '2014-10-01 00:00:00'
        ),
        'updated_date' => array(
            'visible' => false,
            'value' => '2014-10-01 00:00:00'
        ),
        'tournament_competition_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'external_event_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'event_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'wagering_api_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'score' => array(
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
        'start_date' => 'required'
    ),


    /**
     * The filter set
     */
    'filters' => array(

        'name' => array(
            'title' => 'Event Name',
            'type' => 'text'
        ),
//        'competitions' => array(
//            'title' => 'Comeption Name',
//            'type' => 'relationship',
//            'name_field' => 'name',
//            'autocomplete' => true,
//            'num_options' => 5,
//        ),
        'start_date' => array(
            'title' => 'Start Date',
            'type' => 'datetime',
            'date_format' => 'yy-mm-dd',
            'time_format' => 'HH:mm',
        ),
        'display_flag' => array(
            'title' => 'Display on TopBetta',
            'type' => 'bool',
        )
    ),


    'query_filter'=> function($query)
    {
        $query->whereNull('number');
    },


);