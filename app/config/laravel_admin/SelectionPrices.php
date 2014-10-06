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

    'title' => 'Selection Price',

    'single' => 'selection price',

    'model' => '\TopBetta\SportsSelectionPrice',

    /**
     * The display columns
     */
    'columns' => array(
        'id',

        'selection_name' => array(
            'title' => 'Selection Name',
            'relationship' => 'selections',
            'select' => '(:table).name',
        ),

        'event_name' => array(
            'title' => 'Event Name',
            'relationship' => 'selections.markets.events',
            'select' => '(:table).name',
        ),

        'event_date' => array(
            'title' => 'Event Date',
            'relationship' => 'selections.markets.events',
            'select' => '(:table).start_date',
        ),

        'market_type_name' => array(
            'title' => 'Market Type Name',
            'relationship' => 'selections.markets.markettypes',
            'select' => '(:table).name',
        ),

        'win_odds' => array(
            'title' => 'Odds',
            'type' => 'number',

        ),
    ),

    'edit_fields' => array(

        'selections' => array(
            'title' => 'Selection Name',
            'type' => 'relationship',
            'name_field' => 'name',
            'autocomplete' => true,
            'num_options' => 5,

        ),

        'win_odds' => array(
           'title' => 'Odds',
           'type' => 'number',
           'symbol' => '$', //optional, defaults to ''
           'decimals' => 2, //optional, defaults to 0
           'thousands_separator' => ',', //optional, defaults to ','
           'decimal_separator' => '.',
        ),

        'w_product_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'p_product_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'line' => array(
            'visible' => false,
            'value' => '0'
        ),
        'created_date' => array(
            'visible' => false,
            'value' => '2014-10-01 00:00:00'
        ),


    ),

    /**
     * The sort options for a model
     *
     * @type array
     */
    'sort' => array(
        'field' => 'id',
        'direction' => 'desc',
    ),
//
//    /**
//     * The validation rules for the form, based on the Laravel validation class
//     *
//     * @type array
//     */
////    'rules' => array(
////        'name' => 'required|max:64',
////        'description' => 'required|max:128'
////    ),
//
//
    /**
     * The filter set
     */
    'filters' => array(

        'selections' => array(
            'title' => 'Selection Name',
            'type' => 'relationship',
            'name_field' => 'name',
            'autocomplete' => true,
            'num_options' => 5,
        ),

    ),

    'query_filter'=> function($query)
    {
        $query->whereNull('place_odds');
    },

);