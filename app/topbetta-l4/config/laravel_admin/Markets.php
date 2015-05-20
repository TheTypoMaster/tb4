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

    'title' => 'Markets',

    'single' => 'market',

    'model' => '\TopBetta\SportsMarket',

    /**
     * The display columns
     */
    'columns' => array(
        'id',

        'market_type_name' => array(
            'title' => 'Market Type Name',
            'relationship' => 'markettypes',
            'select' => '(:table).name',
        ),

        'event_name' => array(
            'title' => 'Event Name',
            'type' => 'text',
            'relationship' => 'events',
            'select' => '(:table).name',
        ),

        'event_date' => array(
            'title' => 'Event Start',
            'type' => 'text',
            'relationship' => 'events',
            'select' => '(:table).start_date',
        ),



        'market_status' => array(
            'title' => 'Market Status'
        ),

        'line' => array(
            'title' => 'Line'
        ),

        'display_flag' => array(
            'title' => 'Available on TopBetta'
        )
    ),

    'edit_fields' => array(

        'events' => array(
            'title' => 'Event Name',
            'type' => 'relationship',
            'autocomplete' => true,
            'num_options' => 5,
        ),

        'markettypes' => array(
            'title' => 'Market Type Name',
            'type' => 'relationship',
            'autocomplete' => true,
            'num_options' => 5,
        ),

        'market_status' => array(
            'title' => 'Market Status',
            'type' => 'text'
        ),

        'line' => array(
            'title' => 'Line',
            'type' => 'text'
        ),

        'display_flag' => array(
            'title' => 'Available on TopBetta',
            'type' => 'bool'
        )
    ),

    /**
     * The sort options for a model
     *
     * @type array
     */
    'sort' => array(
        'field' => 'created_at',
        'direction' => 'desc',
    ),

    /**
     * The validation rules for the form, based on the Laravel validation class
     *
     * @type array
     */
//    'rules' => array(
//        'name' => 'required|max:64',
//        'description' => 'required|max:128'
//    ),


    /**
     * The filter set
     */
    'filters' => array(

        'markettypes' => array(
            'title' => 'Market Type Name',
            'type' => 'relationship',
            'name_field' => 'name',
            'autocomplete' => true,
            'num_options' => 30,
            'options_filter' => function($query)
                {
                    $query->where('id', '!=', '110');
                },
        ),

        'events' => array(
            'title' => 'Event Name',
            'type' => 'relationship',
            'name_field' => 'name',
            'autocomplete' => true,
            'num_options' => 30,
            'options_filter' => function($query)
            {
                $query->whereNull('number');
            },
        ),

        'eventsdate' => array(
            'title' => 'Event Date',
            'type' => 'relationship',
            'name_field' => 'start_date',
            'autocomplete' => true,
            'num_options' => 30,
            'options_filter' => function($query)
            {
                $query->whereNull('number');
            },
        ),


        'market_status' => array(
            'title' => 'Market Status',
            'type' => 'text'
        ),

        'line' => array(
            'title' => 'Line',
            'type' => 'text'
        ),

        'display_flag' => array(
            'title' => 'Show on TopBetta',
            'type' => 'bool'
        )
    ),

    'query_filter'=> function($query)
    {
        $query->where('market_type_id', '!=', '110');
    },
);