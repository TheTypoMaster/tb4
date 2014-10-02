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

    'model' => '\TopBetta\SportsComps',

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
            'title' => 'Sport Name',
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
        'sport_id' => 'required|integer'
    ),


    /**
     * The filterable fields
     *
     * @type array
     */
    'filters' => array(

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

    /**
     * The filter set
     */
//    'filters' => array(
//        'id',
//        'first_name' => array(
//            'title' => 'First Name',
//        ),
//        'last_name' => array(
//            'title' => 'Last Name',
//        ),
//        'films' => array(
//            'title' => 'Films',
//            'type' => 'relationship',
//            'name_field' => 'name',
//        ),
//        'birth_date' => array(
//            'title' => 'Birth Date',
//            'type' => 'date'
//        ),
//    ),
//
//    /**
//     * The editable fields
//     */
//    'edit_fields' => array(
//        'first_name' => array(
//            'title' => 'First Name',
//            'type' => 'text',
//        ),
//        'last_name' => array(
//            'title' => 'Last Name',
//            'type' => 'text',
//        ),
//        'birth_date' => array(
//            'title' => 'Birth Date',
//            'type' => 'date',
//        ),
//        'films' => array(
//            'title' => 'Films',
//            'type' => 'relationship',
//            'name_field' => 'name',
//        ),
//    ),

);