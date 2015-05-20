<?php

namespace TopBetta\Http\Frontend\Controllers;

use TopBetta;
use Password;
use Hash;
use Input;
use User;
use Lang;
use View;

class FrontPasswordResetsController extends \BaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {

        $input = Input::json()->all();

        $rules = array('email' => 'required');

        $validator = \Validator::make($input, $rules);

        if ($validator->fails()) {

            return array("success" => false, "error" => $validator->messages()->all());
        } else {
            $user = User::where('email', '=', $input['email']);
            if (! $user->count()) {
                return array('success' => false, 'error' => Lang::get('reminders.user'));
            }

            $user = $user->first();
            //View composer to inject the username of the user into the email view
            View::composer(array('emails.auth.reminder', 'emails.auth.reminder_toptippa'), function ($view) use ($user){

                $view->with('username', $user->username);
            });

            if(isset($input['custom_remind_message'])){
                \Config::set('auth.reminder.email', 'emails.auth.reminder_toptippa');
                \Config::set('mail.from.address', 'help@toptippa.com.au');
                \Config::set('mail.from.name', 'TopTippa');
            }

            Password::remind(array('email' => $input['email']), function($message) {
                $message->subject('Your Password Reminder');
            });

            return array('success' => true, 'result' => Lang::get('reminders.email_sent'));
        }
    }

    public function postReset()
    {
        // ******** gone my own way as the native Laravel password reset
        // ******** was throwing excpetions
        $creds = Input::json()->all();

        $rules = array('email' => 'required', 'password_confirmation' => 'required|same:password', 'token' => 'required');
        $rules['password'] = array('required', 'min:5', 'regex:([a-zA-Z].*[0-9]|[0-9].*[a-zA-Z])');

        $validator = \Validator::make($creds, $rules);

        if ($validator->fails()) {

            return array("success" => false, "error" => $validator->messages()->all());
        } else {

            $users = User::where('email', '=', $creds['email'])->get();

            if (!$users) {
                return array('success' => false, 'error' => array(Lang::get('reminders.user')));
            }

            // check reminder table for email and token match
            $match = \DB::table('tb_password_reminders')
                    ->where('email', $creds['email'])
                    ->where('token', $creds['token'])
                    ->first();

            if (!$match) {
                return array('success' => false, 'error' => array(Lang::get('reminders.token')));
            }

            // generate a password via Joomla legacy method
            $l = new \TopBetta\LegacyApiHelper;
            $genPassword = $l->query('generateJoomlaPassword', array('password' => $creds['password']));

            if ($genPassword['status'] == 200) {
                $password = $genPassword['joomla_password'];
            } else {
                return array('success' => false, 'error' => array(Lang::get('reminders.password_error')));
            }

            // Save user object
            $user = $users[0];
            $user->password = $password;
            $user->save();

            // remove the entry from password reminder table
            $match = \DB::table('tb_password_reminders')
                    ->where('email', $creds['email'])
                    ->delete();

            return array('success' => true, 'result' => Lang::get('reminders.password_changed'));
        }
    }

}
