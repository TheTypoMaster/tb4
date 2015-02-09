<?php namespace TopBetta\admin\Controllers;

use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;

use View;
use Request;
use Input;
use Redirect;

class MarketTypeController extends \BaseController {

	/**
	 * @var MarketTypeRepositoryInterface
	 */
	private $marketTypeRepository;

	public function __construct(MarketTypeRepositoryInterface $marketTypeRepository)
	{
		$this->marketTypeRepository = $marketTypeRepository;
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q', '');
		if ($search) {
			$marketTypes = $this->marketTypeRepository->searchMarketTypes($search);
		} else {
			$marketTypes = $this->marketTypeRepository->allMarketTypes();
		}

		return View::make('admin::eventdata.markettypes.index', compact('marketTypes', 'search'));
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
		//Get search string for filtering after redirect
		$search = Input::get("q", '');

		$marketType = $this->marketTypeRepository->getMarketTypeById($id);

		if(is_null($marketType)) {
			return Redirect::route("admin.markettypes.index");
		}

		return View::make("admin::eventdata.markettypes.edit")->with(compact('marketType', 'search'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//Get search string for filtering after redirect
		$search = Input::get("q", '');

		$data = Input::only('name', 'description', 'ordering');

		$data['ordering'] = $data['ordering'] == '' ? null : $data['ordering'];
		$this->marketTypeRepository->updateWithId($id, $data);

		return Redirect::route('admin.markettypes.index', array($id, "q"=>$search))
			->with('flash_message', 'Saved!');
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
