<?php namespace TopBetta;

class LegacyApiHelper {

	protected $allowed_methods = array('doUserLogin' => 'post', 'getLoginHash' => 'get', 'getUser' => 'get', 'saveBet' => 'post', 'saveTournamentTicket' =>'post');

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

			switch ($method) {
				//Handle any special cases
				case 'doUserLogin' :

					//1. get login hash
					$login_hash = $this -> curl('getLoginHash', $this -> allowed_methods['getLoginHash'], $payload, false);

					//2. perform login
					$payload[$login_hash['login_hash']] = 1;
					return $this -> curl('doUserLogin', $this -> allowed_methods['doUserLogin'], $payload);

					break;
					
				case 'saveBet' :

					//1. get login hash
					$login_hash = $this -> curl('getLoginHash', $this -> allowed_methods['getLoginHash'], $payload, false);

					//2. save bet
					$payload[$login_hash['login_hash']] = 1;
					return $this -> curl('saveBet', $this -> allowed_methods['saveBet'], $payload);

					break;					

				case 'saveTournamentTicket' :

					//1. get login hash
					$login_hash = $this -> curl('getLoginHash', $this -> allowed_methods['getLoginHash'], $payload, false);

					//2. save bet
					$payload[$login_hash['login_hash']] = 1;
					return $this -> curl('saveTournamentTicket', $this -> allowed_methods['saveTournamentTicket'], $payload);

					break;	

				default :

					//pass api call straight through
					return $this -> curl($method, $this -> allowed_methods[$method], $payload);

					break;
			}


		} else {

			return array('status' => 500, 'error_msg' => 'Invalid legacy method');
		}
	}

	/*
	 * Our curl call to handle post & get requests separately
	 *
	 * - we are using a cookie file that we create for each user dynamically before being used
	 * - this is stored in the database so it will work across the server pool
	 */
	private function curl($method, $type, $payload = array(), $del_cookie = true) {

		//TODO: we should have a cookie file prepared for us unique to this users session

		$url = \URL::to('/api/?method=') . $method;

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
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'tmp/' . $payload['username'] . '.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'tmp/' . $payload['username'] . '.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$buffer = json_decode(curl_exec($ch), true);

		curl_close($ch);

		//TODO: we are done with the cookie file, delete it now
		if ($del_cookie) {
			//rm the cookie file
		}

		return $buffer;

	}

}
