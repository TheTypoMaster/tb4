<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 1/10/2015
 * Time: 11:33 AM
 */

namespace TopBetta\Http\Controllers\Frontend;

use Auth;
use Illuminate\Http\Request;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Response\ApiResponse;

class PusherAuthenticationController extends Controller {

    /**
     * @var Pusher
     */
    private $pusher;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(\Pusher $pusher, ApiResponse $response)
    {
        $this->pusher = $pusher;
        $this->response = $response;
    }

    /**
     * Pusher authentication for private channels
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        if ($user = \Auth::user()) {
            $channel = $request->get('channel_name');
            $channelUserId = explode('_', $channel);
            $channelUserId = $channelUserId[count($channelUserId) - 1];

            if ($user->id == $channelUserId) {
                return $this->response->success(
                    $this->pusher->socket_auth($channel, $request->get('socket_id'))
                );
            }
        }

        return $this->response->failed("Forbidden", 403);
    }
}