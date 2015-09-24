<?php namespace TopBetta\Services\Feeds\Racing;
/**
 * Coded by Oliver Shanahan
 * File creation date: 03/04/15
 * File creation time: 14:26
 * Project: tb4
 */

use Illuminate\Support\Facades\Validator;
use Log;
use File;
use Carbon;
use Queue;

use TopBetta\Jobs\Pusher\Racing\PriceSocketUpdate;
use TopBetta\Repositories\Cache\RaceRepository;
use TopBetta\Repositories\Cache\RacingSelectionPriceRepository;
use TopBetta\Repositories\Cache\RacingSelectionRepository;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Helpers\RiskManagerAPI;
use TopBetta\Resources\PriceResource;

class RacePriceProcessingService {

    protected $events;
    protected $selections;
    protected $betproduct;
    protected $prices;
    protected $riskhelper;


    public function __construct(RaceRepository $events,
                                RacingSelectionRepository $selections,
                                BetProductRepositoryInterface $betproduct,
								RacingSelectionPriceRepository $prices,
                                RiskManagerAPI $riskhelper){


        $this->events = $events;
        $this->selections = $selections;
        $this->betproduct = $betproduct;
        $this->prices = $prices;
        $this->riskhelper = $riskhelper;
        $this->logprefix = 'RacePriceProcessingService ';
    }

    /**
     * Pass payload to correct method for processing
     *
     * @param $data
     * @return string|void
     */
    public function processRacingData($data){
        //\Log::info(print_r($data,true));
        //Log::debug('Processing Payload');
        foreach ($data as $key => $racingData) {

            switch ($key) {
                case 'MeetingList':
                    return $this->_processMeetingData(($racingData));
                    break;
                case 'RaceList':
                    return $this->_processRaceData(($racingData));
                    break;
                case 'RunnerList':
                    return $this->_processRunnerData(($racingData));
                    break;
                case 'PriceList':
                    return $this->_processPriceData(($racingData));
                    break;
            }

        }
    }

	/**
	 * Process price data
	 *
	 * @param $prices
	 * @return string
	 */
	private function _processPriceData($prices){

        $updates = array();

        Log::debug($this->logprefix . '(_processPriceData): ', $prices);
		foreach ($prices as $price) {
			/*
			 * validate runner payload
			 */
			$rules = array('MeetingId' => 'required',
                            'RaceNo' => 'required|integer',
                            'BetType' => 'required',
                            'PriceType' => 'required',
                            //'PoolAmount' => 'required',
                            'OddString' => 'required');
			$validator = Validator::make($price, $rules);
			if ($validator->fails()) {
				Log::debug($this->logprefix . '(_processPriceData): Price data incomplete - ' . $validator->messages(), $price);
				continue;
			}

			// explode the odds string
			$oddsArray = explode(';', $price['OddString']);

			if (!is_array($oddsArray)) {
				Log::debug($this->logprefix . '(_processPriceData): Price data odds incomplete ', $price);
				continue;
			}

			// check if race exists in DB
			$existingRaceDetails = $this->events->getEventDetailByExternalId($price['MeetingId'] . '_' . $price['RaceNo']);
			if (!$existingRaceDetails) {
				Log::debug($this->logprefix . '(_processPriceData): Race for price not found ' . $price['MeetingId'] . '_' . $price['RaceNo']);
				continue;
			}
            if (!array_get($updates, $existingRaceDetails['id'])) {
                $updates[$existingRaceDetails['id']] = array();
            }


			$runnerCount = 1;

            $betProduct = $this->betproduct->getProductByCode($price['PriceType']);
            if (!$betProduct) {
                Log::debug($this->logprefix . '(_processPriceData): PriceType not found ' . $price['PriceType']);
                continue;
            }

           // Log::info($this->logprefix ."(_processPriceData): Processing Odds. USED: MeetID:{$price['MeetingId']}, RaceNo:{$price['RaceNo']}, BetType:{$price['BetType']}, PriceType:{$price['PriceType']}, Odds:" . $price['OddString']);

			// loop on each runners odds
			foreach ($oddsArray as $runnerOdds) {

				// ignore odds of 0
				if($runnerOdds == '0'){
                    $runnerCount++;
					continue;
				}

				// check if selection exists
				$existingSelection = $this->selections->getSelectionByExternalId($price['MeetingId'] . '_' . $price['RaceNo'].'_'.$runnerCount);

				if(!$existingSelection) {
					Log::debug($this->logprefix . '(_processPriceData): Selection for price missing', $price);
					continue;

				}

				$priceDetails = array("bet_product_id" => $betProduct->id);
				$priceDetails['selection_id'] = $existingSelection->id;
				switch ($price['BetType']) {
					case "W":
						$priceDetails['win_odds'] = $runnerOdds / 100;
						break;
					case "P":
						$priceDetails['place_odds'] = $runnerOdds / 100;
						break;
					default:
						Log::debug($this->logprefix . '(_processPriceData): Price BetType is invalid ', $price);
						continue;
				}

                $priceModel = $this->prices->getPriceForSelectionByProduct($existingSelection->id, $betProduct->id);

                if ($priceModel && $priceModel->fill($priceDetails)->isDirty()) {
                    $priceModel = $this->prices->update($priceModel, $priceDetails);
                    $this->selections->updatePricesForSelectionInRace($existingSelection->id, $existingRaceDetails, $priceModel);
                    $updates[$existingRaceDetails['id']][] =  array("id" => $priceModel->selection_id, "prices" => (new PriceResource($priceModel))->toArray());
                } else if (!$priceModel) {
                    $priceModel = $this->prices->create($priceDetails);
                    $this->selections->updatePricesForSelectionInRace($existingSelection->id, $existingRaceDetails, $priceModel);
                    $updates[$existingRaceDetails['id']][] = array("id" => $priceModel->selection_id, "prices" => (new PriceResource($priceModel))->toArray());
                }
                $runnerCount++;
			}
            /*
                * Push Fixed odds updates to risk
                * - only push TB fixed odds
                * - only push if the price is not already overridden
                */

            // check for fixed odds
            if($betProduct->is_fixed_odds == 0) continue;

            // put on the queue
            Queue::push('TopBetta\Services\Feeds\Queues\RiskManagerPushAPIQueueService', array('PriceList' => array($price)), 'risk-fixed-queue');
		}

        foreach($updates as $race=>$selections) {
            if (count($selections)) {
                \Bus::dispatch(new PriceSocketUpdate(array("id" => $race, "selections" => $selections)));
            }
        }

		return "Price(s) Processed";
	}
}