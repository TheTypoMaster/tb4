<?php

namespace TopBetta\admin\controllers;

use View;
use Input;
use Redirect;
use TopBetta\Repositories\Contracts\ConfigurationRepositoryInterface;
use TopBetta\Services\Tournaments\TournamentBuyInRulesService;

class TournamentSettingsController extends \BaseController {

    /**
     * @var ConfigurationRepositoryInterface
     */
    private $configurationRepository;

    public function __construct(ConfigurationRepositoryInterface $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }



	/**
	 * Show the form for editing the specified resource.

	 * @return Response
	 */
	public function edit()
	{
		$config = $this->configurationRepository->getConfigByName(TournamentBuyInRulesService::CONFIG_NAME, true);

        return View::make('admin::tournaments.settings.edit', compact('config'));
	}


	/**
	 * Update the specified resource in storage.

	 * @return Response
	 */
	public function update()
	{
		$data = Input::except(array('_method', '_token'));

        $config = $this->configurationRepository->getByName(TournamentBuyInRulesService::CONFIG_NAME);

        $config->values = json_encode($data);

        $config->save();

        return Redirect::to('/admin/tournament-settings')
            ->with(array('flash_message' => "Saved"));
	}


}
