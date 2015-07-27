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

    'title' => 'Link Competitions & Events',

    'single' => 'competition-event',

    'model' => '\TopBetta\Models\SportEventGroupEvent',

    /**
     * The display columns
     */
    'columns' => array(



        'competition_id' => array(
            'title' => 'Competition Id',
            'relationship' => 'competitions',
            'select' => '(:table).id'
        ),

        'competition_date' => array(
            'title' => 'Competition Date',
            'relationship' => 'competitions',
            'select' => '(:table).start_date'
        ),

        'competition_name' => array(
            'title' => 'Competition Name',
            'relationship' => 'competitions',
            'select' => '(:table).name'
        ),

        'event_id' => array(
            'title' => 'Event Id',
            'relationship' => 'events',
            'select' => '(:table).id'
        ),

        'event_date' => array(
            'title' => 'Event Date',
            'relationship' => 'events',
            'select' => '(:table).start_date'
        ),

        'event_name' => array(
            'title' => 'Event Name',
            'relationship' => 'events',
            'select' => '(:table).name'
        ),

    ),

    'edit_fields' => array(

        'competitions' => array(
            'title' => 'Competition Name',
            'type' => 'relationship',
            'autocomplete' => true,
            'num_options' => 5,
            'name_field' => 'name'
        ),
        'events' => array(
            'title' => 'Event Name',
            'type' => 'relationship',
            'autocomplete' => true,
            'num_options' => 5,
            'name_field' => 'name'
        ),
    ),

    /**
     * The sort options for a model
     *
     * @type array
     */
    'sort' => array(
        'field' => 'event_group_id',
        'direction' => 'desc',
    ),

    /**
     * The validation rules for the form, based on the Laravel validation class
     *
     * @type array
     */
    'rules' => array(
        'event_group_id' => 'required|integer',
        'event_id' => 'required|integer'
    ),


    /**
     * The filterable fields
     *
     * @type array
     */
    'filters' => array(
       'event_group_id' => array(
           'title' => 'Competition ID',
           'type' => 'number',
       ),
//        'events' => array(
//            'title' => 'Event Name',
//            'type' => 'relationship',
//            'autocomplete' => true,
//            'num_options' => 5,
//        ),
    ),


);