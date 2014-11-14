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

    'title' => 'Selections',

    'single' => 'selection',

    'model' => '\TopBetta\SportsSelection',

    /**
     * The display columns
     */
    'columns' => array(
        'id',

        'name' => array(
            'title' => 'Selection Name'
        ),
//
        'event_id' => array(
            'title' => 'Event Id',
            'relationship' => 'markets.events',
            'select' => '(:table).id',
        ),

        'event_name' => array(
            'title' => 'Event',
            'relationship' => 'markets.events',
            'select' => '(:table).name',
        ),

        'market_id' => array(
            'title' => 'Market Id',
            'relationship' => 'markets',
            'select' => '(:table).id',
        ),

        'market_type_name' => array(
            'title' => 'Market',
            'relationship' => 'markets.markettypes',
            'select' => '(:table).name',
        ),

        'selection_price' => array(
            'title' => 'Odds',
            'relationship' => 'selectionprice',
            'select' => '(:table).win_odds',
            'decimals' => 2, //optional, defaults to 0
            'decimal_separator' => '.',
        ),

//        'selection_result' => array(
//            'title' => 'Win Dividend',
//            'relationship' => 'selectionresult',
//            'select' => '(:table).win_dividend',
//        ),

        'image_url' => array(
            'title' => 'Image URL'
        ),

        'order' => array(
            'title' => 'Order'
        ),

        'selection_status' => array(
            'title' => 'Status',
            'relationship' => 'selectionstatus',
            'select' => '(:table).name',
        ),
    ),


    'edit_fields' => array(

        'name' => array(
            'title' => 'Selection Name',
            'type' => 'text',
        ),

        'markets' => array(
            'title' => 'Market Id',
            'type' => 'relationship',
            'name_field' => 'id',
            'autocomplete' => true,
            'num_options' => 5,

        ),

//        'selectionprice' => array(
//            'title' => 'Price',
//            'type' => 'relationship',
//            'name_field' => 'win_odds'
//        ),

        'image_url' => array(
            'title' => 'Image URL',
            'type' => 'text'
        ),

        'order' => array(
            'title' => 'Order Number',
            'type' => 'number'
        ),

        'selectionstatus' => array(
            'title' => 'Status',
            'type' => 'relationship'
        ),

        // non-nullable fields
        'external_selection_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'wagering_api_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'wager_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'silk_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'created_date' => array(
            'visible' => false,
            'value' => '2014-10-01 00:00:00'
        ),
        'weight' => array(
            'visible' => false,
            'value' => '0'
        ),
        'trainer' => array(
            'visible' => false,
            'value' => '0'
        ),
        'last_starts' => array(
            'visible' => false,
            'value' => ''
        ),
        'external_event_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'external_market_id' => array(
            'visible' => false,
            'value' => '0'
        ),
        'home_away' => array(
            'visible' => false,
            'value' => '0'
        ),
        'bet_type_ref' => array(
            'visible' => false,
            'value' => ''
        ),
        'bet_place_ref' => array(
            'visible' => false,
            'value' => '0'
        ),
        'runner_code' => array(
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

//        'markettypes' => array(
//            'title' => 'Market Type Name',
//            'type' => 'relationship',
//            'name_field' => 'name',
//            'autocomplete' => true,
//            'num_options' => 5,
//            'options_filter' => function($query)
//                {
//                    $query->where('id', '!=', '110');
//                },
//        ),

//        'markets.events' => array(
//            'title' => 'Event Name',
//            'type' => 'relationship',
//            'name_field' => 'name',
//            'autocomplete' => true,
//            'num_options' => 5,
//        ),
        'name' => array(
            'title' => 'Selection Name',
            'type' => 'text'
        ),

//        'selectionstatus' => array(
//            'title' => 'Selection Status',
//            'type' => 'relationship'
//        ),


    ),

    'query_filter'=> function($query)
    {
        $query->whereNull('number');
    },
);