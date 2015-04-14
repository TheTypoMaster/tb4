<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:28 AM
 */

namespace TopBetta\backend;

use Input;
use TopBetta\Services\Feeds\SportsFeedService;

class SportsFeedController extends \BaseController {

    /**
     * @var SportsFeedService
     */
    private $sportsFeedService;

    public function __construct(SportsFeedService $sportsFeedService)
    {
        $this->sportsFeedService = $sportsFeedService;
    }

    public function store()
    {
        $data = Input::json()->all();

        $this->sportsFeedService->processSportsFeed($data);

        //TODO: Is Serena expecting a particular format?
        return "true";
    }
}