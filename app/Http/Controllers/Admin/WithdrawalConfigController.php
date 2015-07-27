<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Accounting\WithdrawalService;
use View;
use Input;
use Redirect;
use TopBetta\Repositories\Contracts\ConfigurationRepositoryInterface;

class WithdrawalConfigController extends Controller {

    /**
     * @var ConfigurationRepositoryInterface
     */
    private $configurationRepository;

    public function __construct(ConfigurationRepositoryInterface $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$config = $this->configurationRepository->getConfigByName(WithdrawalService::WITHDRAWAL_EMAIL_CONFIG);

        $variables = $this->configurationRepository->getConfigByName(WithdrawalService::WITHDRAWAL_EMAIL_VARIABLE_CONFIG, true);

        return View::make('admin.withdrawals.config.edit', compact('config', 'variables'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$data = Input::except(array('_token', '_method'));

        $this->configurationRepository->updateWithId(
            $this->configurationRepository->getIdByName(WithdrawalService::WITHDRAWAL_EMAIL_CONFIG),
            array("values" => json_encode($data))
        );

        return Redirect::route('admin.withdrawals.index');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
