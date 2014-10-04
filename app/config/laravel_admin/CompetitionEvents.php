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

    'model' => '\TopBetta\SportEventGroupEvent',

    /**
     * The display columns
     */
    'columns' => array(

        'competition_name' => array(
            'title' => 'Competition Name',
            'relationship' => 'competitions',
            'select' => '(:table).name'
        ),
        'event_name' => array(
            'title' => 'Event Name',
            'relationship' => 'events',
            'select' => '(:table).name'
        ),
        'event_date' => array(
            'title' => 'Event Date',
            'relationship' => 'events',
            'select' => '(:table).start_date'
        )
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
//    'rules' => array(
//        'name' => 'required|max:64',
//        'sport_id' => 'required|integer'
//    ),


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