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

        'event_name' => array(
            'title' => 'Event Name',
            'relationship' => 'markets.events',
            'select' => '(:table).name',
        ),

        'market_type_name' => array(
            'title' => 'Market Type Name',
            'relationship' => 'markets.markettypes',
            'select' => '(:table).name',
        ),

        'selection_price' => array(
            'title' => 'Fixed Odds',
            'relationship' => 'selectionprice',
            'select' => '(:table).win_odds',
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
            'title' => 'Order Number'
        ),

        'selection_status_id' => array(
            'title' => 'Selection Status'
        ),
//
//        'line' => array(
//            'title' => 'Line'
//        ),
//
//        'display_flag' => array(
//            'title' => 'Available on TopBetta'
//        )
    ),

    'edit_fields' => array(

        'name' => array(
            'title' => 'Selection Name',
            'type' => 'text',
        ),

//        'selectionprice' => array(
//            'title' => 'Price',
//            'type' => 'relationship',
//            'name_field' => 'win_odds'
//        ),

        'image_url' => array(
            'title' => 'Image URL',
            'type' => 'number'
        ),

        'order' => array(
            'title' => 'Order Number',
            'type' => 'number'
        ),

        'selection_status' => array(
            'title' => 'Status',
            'type' => 'number'
        ),

//        'line' => array(
//            'title' => 'Line',
//            'type' => 'text'
//        ),
//
//        'display_flag' => array(
//            'title' => 'Available on TopBetta',
//            'type' => 'bool'
//        )
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

        'selection_status' => array(
            'title' => 'Selection Status',
            'type' => 'text'
        ),


    ),

    'query_filter'=> function($query)
    {
        $query->whereNull('number');
    },
);