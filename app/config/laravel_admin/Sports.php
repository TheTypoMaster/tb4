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

    'title' => 'Sports',

    'single' => 'sport',

    'model' => '\TopBetta\TournamentSport',

    /**
     * The display columns
     */
    'columns' => array(
        'id',
        'name' => array(
            'title' => 'Sport Name'
        ),
        'description' => array(
            'title' => 'Description'
        ),

        'status_flag' => array(
            'title' => 'Available for Tournaments'
        )
    ),

    'edit_fields' => array(
        'name' => array(
            'title' => 'Sport Name',
            'type' => 'text'
        ),
        'description' => array(
            'title' => 'Descriptoin',
            'type' => 'text'
        ),
        'status_flag' => array(
            'title' => 'Status Flag',
            'type' => 'bool'
        ),
        'created_date' => array(
            'visible' => false,
            'value' => '2014-10-01 00:00:00'
        ),
        'updated_date' => array(
            'visible' => false,
            'value' => '2014-10-01 00:00:00'
        )
    ),

    /**
     * The sort options for a model
     *
     * @type array
     */
    'sort' => array(
        'field' => 'name',
        'direction' => 'asc',
    ),

    /**
     * The validation rules for the form, based on the Laravel validation class
     *
     * @type array
     */
    'rules' => array(
        'name' => 'required|max:64',
        'description' => 'required|max:128'
    ),


    /**
     * The filter set
     */
    'filters' => array(
        'name' => array(
            'title' => 'Sport Name',
            'type' => 'text'
        ),
        'description' => array(
            'title' => 'Descriptoin',
            'type' => 'text'
        ),
        'status_flag' => array(
            'title' => 'Status Flag',
            'type' => 'bool'
        ),
    ),

);