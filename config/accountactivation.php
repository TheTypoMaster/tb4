<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/02/2015
 * Time: 3:45 PM
 */

return array(


    "default_source" =>  env('ACTIVATION_SOURCE', 'topbetta'),


    "activation_url" => env('ACTIVATION_URL', 'https://services.dev/api/v1/'),


    /*
	 * ----------------------------------------
	 * Welcome emails
	 * -----------------------------------------
	 * Holds all the info for different sources for the welcome email
	 */

    "email" => array(

        "topbetta" => array(

            "email"		=> "emails.welcome.welcome",

            "subject" 	=> "Welcome!"
        ),

    ),
);