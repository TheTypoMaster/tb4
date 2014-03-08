<?php

/**
 * @version		$Id: riskmanager.php  Michael Costa $
 * @package		
 * @subpackage	Admin
 * @copyright	Copyright (C) 2014 TopBetta. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
class RiskManagerHelper
{

    static $riskManagerAPI = 'http://risk.mugbookie.com/api/v1/';
    static $productionHost = 'services.topbetta.com.au';
//    static $productionHost = 'testing1.mugbookie.com';

    public function sendRacingBet($betData)
    {
        $response = RiskManagerHelper::curl('bets', 'post', $betData);

        if (!$response) {
            return false;
        }
        
        if ($response->status == 200) {
            return true;
        } else {
            return false;
        }
    }

    private function curl($endPoint, $type, $payload = array())
    {
        // we only want to send to risk manager for production
        if ($_SERVER['HTTP_HOST'] != RiskManagerHelper::$productionHost) {
            return false;
        }
        $url = RiskManagerHelper::$riskManagerAPI . $endPoint;

        $ch = curl_init();
        if ($type == 'post') {
            //send through our payload as post fields
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        } else {
            //send through our payload as query string
            $url .= '&' . http_build_query($payload);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $buffer = json_decode(curl_exec($ch));

        curl_close($ch);

        return $buffer;
    }

}

?>
