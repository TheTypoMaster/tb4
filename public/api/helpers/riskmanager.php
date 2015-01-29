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

	/**
	 * array(HOSTNAME => RISK_API_ENDPOINT)
	 * 
	 * @var array
	 */
	static $riskConfig = array(
		'services.topbetta.com.au' => 'http://risk.mugbookie.com/api/v1/',
		'testing1.mugbookie.com' => 'http://riskier.mugbookie.com/api/v1/',
		'tb4.dev' => 'http://riskmanager.dev/api/v1/',
        'localhost' => 'http://risk.dev/api/v1/'
	);

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

    public function sendSportBet($betData)
    {
        $response = RiskManagerHelper::curl('sportbets', 'post', $betData);

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
        // map the right endpoint to this host
		if (!array_key_exists($_SERVER['HTTP_HOST'], RiskManagerHelper::$riskConfig)) {
			return false;
		}

        $url = RiskManagerHelper::$riskConfig[$_SERVER['HTTP_HOST']] . $endPoint;

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
