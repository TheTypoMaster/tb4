<?php namespace TopBetta;

use Config;
use TopBetta\Services\Exceptions\UnauthorizedAccessException;

class LegacyApiHelper {

	protected $allowed_methods = array('doUserLogin' => 'post', 'doUserRegisterBasic' => 'post', 'doUserRegisterTopBetta' => 'post', 'doInstantDeposit' => 'post', 'doWithdrawRequest' => 'post', 'getLoginHash' => 'post', 'getUser' => 'get', 'saveBet' => 'post', 'saveRacingBet' => 'post', 'saveSportBet' => 'post', 'saveTournamentBet' => 'post', 'saveTournamentSportsBet' => 'post', 'saveTournamentTicket' =>'post', 'setBetLimit' => 'post', 'doSelfExclude' => 'post', 'generateJoomlaPassword' => 'post', 'doReferFriend' => 'post', 'getBettingHistory' => 'post', 'doUserUpgradeTopBetta' => 'post');

	/*
	 * @param string $method
	 * @param string $payload
	 * @param string $type
	 *
	 * @return Array
	 */
	public function query($method, $payload = array()) {

		$ret = array();

		if (array_key_exists($method, $this -> allowed_methods)) {
            try {
                switch ($method) {
                    //Handle any special cases
                    case 'doUserLogin' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. perform login
                        $payload[$login_hash['login_hash']] = 1;
                        return $this->curl('doUserLogin', $this->allowed_methods['doUserLogin'], $payload);

                        break;

                    case 'doUserRegisterBasic' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. perform login
                        $payload[$login_hash['login_hash']] = 1;
                        return $this->curl('doUserRegisterBasic', $this->allowed_methods['doUserRegisterBasic'], $payload);

                        break;

                    case 'doUserRegisterTopBetta' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. perform login
                        $payload[$login_hash['login_hash']] = 1;
                        return $this->curl('doUserRegisterTopBetta', $this->allowed_methods['doUserRegisterTopBetta'], $payload);

                        break;

                    case 'doUserUpgradeTopBetta' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. perform login
                        $payload[$login_hash['login_hash']] = 1;
                        return $this->curl('doUserUpgradeTopBetta', $this->allowed_methods['doUserUpgradeTopBetta'], $payload);

                        break;

                    case 'doInstantDeposit' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. perform login
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('doInstantDeposit', $this->allowed_methods['doInstantDeposit'], $payload);

                        break;

                    case 'doWithdrawRequest' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. perform login
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('doWithdrawRequest', $this->allowed_methods['doWithdrawRequest'], $payload);

                        break;

                    case 'saveBet' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('saveBet', $this->allowed_methods['saveBet'], $payload);

                        break;

                    case 'saveRacingBet' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('saveRacingBet', $this->allowed_methods['saveRacingBet'], $payload);

                        break;

                    case 'saveSportBet' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('saveSportBet', $this->allowed_methods['saveSportBet'], $payload);

                        break;

                    case 'saveTournamentBet' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save tournament bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('saveTournamentBet', $this->allowed_methods['saveTournamentBet'], $payload);

                        break;

                    case 'saveTournamentSportsBet' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save tournament bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('saveTournamentSportsBet', $this->allowed_methods['saveTournamentSportsBet'], $payload);

                        break;

                    case 'saveTournamentTicket' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('saveTournamentTicket', $this->allowed_methods['saveTournamentTicket'], $payload);

                        break;

                    case 'setBetLimit' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('setBetLimit', $this->allowed_methods['setBetLimit'], $payload);

                        break;

                    case 'doSelfExclude' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('doSelfExclude', $this->allowed_methods['doSelfExclude'], $payload);

                        break;

                    case 'generateJoomlaPassword' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        return $this->curl('generateJoomlaPassword', $this->allowed_methods['generateJoomlaPassword'], $payload);

                        break;

                    case 'doReferFriend' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        return $this->curl('doReferFriend', $this->allowed_methods['doReferFriend'], $payload);

                        break;

                    case 'getBettingHistory' :

                        //1. get login hash
                        $login_hash = $this->curl('getLoginHash', $this->allowed_methods['getLoginHash'], $payload, false);

                        //2. save bet
                        $payload[$login_hash['login_hash']] = 1;
                        $payload['l_user_id']               = \Auth::user()->id;
                        return $this->curl('getBettingHistory', $this->allowed_methods['getBettingHistory'], $payload);

                        break;

                    case 'getUser' :

                        $payload['l_user_id'] = \Auth::user()->id;
                        return $this->curl('getUser', $this->allowed_methods['getUser'], $payload);

                        break;

                    default :

                        //pass api call straight through
                        return $this->curl($method, $this->allowed_methods[$method], $payload);

                        break;
                }
            } catch( UnauthorizedAccessException $e) {
                return array("status" => 403, "error_msg" => "Unauthorized Access");
            }


		} else {

			return array('status' => 500, 'error_msg' => 'Invalid legacy method');
		}
	}

	/*
	 * Our curl call to handle post & get requests separately
	 *
	 * - we are using a cookie file that we create for each user based on their session id
	 */
	private function curl($method, $type, $payload = array(), $del_cookie = true) {

	    $url = \URL::to('/api/?method=') . $method;
        //$url = 'http://services.dev/api/?method=' . $method;

        $payload['api_key'] = $this->createHashKey($payload, $method, Config::get("legacyapi.api_key"));

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
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'tmp/' . session_id() . '.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'tmp/' . session_id() . '.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$buffer = json_decode(curl_exec($ch), true);

		curl_close($ch);

        if( array_get($buffer, 'status', null) == 403 ) {
            throw new UnauthorizedAccessException();
        }

		return $buffer;

	}

    private function createHashKey(array $payload, $method, $secret)
    {
        $sanitizedPayload = array();

        array_walk_recursive($payload, function($v) use (&$sanitizedPayload) {
            $sanitizedPayload[] = is_bool($v) ? (int) $v : $v;
        });

        return hash_hmac(
            "sha256",
            implode("", $sanitizedPayload) . $method,
            $secret
        );
    }

}
