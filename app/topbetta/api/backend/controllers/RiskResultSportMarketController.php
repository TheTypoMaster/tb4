<?php

namespace TopBetta\backend;

use Illuminate\Support\Facades\Input;
use TopBetta\Models\Events;
use TopBetta\Repositories\DbMarketsRepository;

class RiskResultSportMarketController extends \BaseController
{
    /**
     * @var DbMarketsRepository
     */
    private $markets;

    function __construct(DbMarketsRepository $markets)
    {
        $this->markets = $markets;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Input::all();
        if (!$input) {
            $input = Input::json()->all();
        }

        $validator = \Validator::make($input, [
            'event_id' => 'required',
            'market_id' => 'required',
            'status' => 'required',
            'score' => 'required',
        ]);

        if ($validator->fails()) {
            return array("success" => false, "error" => "Problem updating results for Market", "messages" => $validator->messages());
        }

        // just to make sure they have the right event before going any further
        if (!Events::find($input['event_id'])) {
            return array("success" => false, "error" => "Problem updating results for Market " . $input['market_id'], "messages" => "Can't find event " . $input['event_id']);
        }

        $errors = $this->markets->resultMarket($input['market_id'], $input['status'], $input['score']);

        if (count($errors)) {
            return array("success" => false, "error" => "Problem updating results for Market " . $input['market_id'], "messages" => $errors);
        }
        return array("success" => true, "result" => "Results updated for Market " . $input['market_id']);
    }
}