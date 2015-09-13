<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/08/2015
 * Time: 10:23 AM
 */

namespace TopBetta\Services\Affiliates\Messaging;

use App;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class AffiliateMessage implements Arrayable {
    /**
    * @var
    */
    protected $affiliate;
    /**
     * @var array
     */
    protected $data;

    public function __construct($affiliate, $data)
    {
        $this->affiliate = $affiliate;
        $this->data = $data;
    }

    public function toArray()
    {
        $array = array(
            "timestamp" => Carbon::now()->toDateTimeString(),
            "data" => $this->data,
        );

        //create the message authentication token
        $messageAuth = App::make('TopBetta\Services\Authentication\MessageAuthenticationService');
        $array['token'] = $messageAuth->createHashedMessage($array['data'], $this->affiliate);

        return $array;
    }
}