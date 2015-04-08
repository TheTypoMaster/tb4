<?php

namespace TopBetta\admin\Controllers;

use TopBetta\Repositories\Contracts\IconRepositoryInterface;
use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;
use View;
use Input;
use Redirect;
use Config;

class IconController extends \BaseController {


    /**
     * @var IconTypeRepositoryInterface
     */
    private $iconTypeRepository;
    /**
     * @var IconRepositoryInterface
     */
    private $iconRepository;

    public function __construct(IconTypeRepositoryInterface $iconTypeRepository, IconRepositoryInterface $iconRepository)
    {
        $this->iconTypeRepository = $iconTypeRepository;
        $this->iconRepository = $iconRepository;
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
        //get the icon types
        $iconTypes = $this->iconTypeRepository->findAll();

        return View::make('admin::eventdata.icons.create', compact('iconTypes'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$search = Input::get('q', '');

        $file = Input::file('icon_file', null);

        //check file is valid
        if( ! $file || ! $file->isValid() ) {
            return Redirect::route('admin.icons.create')
                ->with(array('flash_message' => 'please select valid file'));
        }

        try {
            $file->move(Config::get('images.image_path'), $file->getClientOriginalName());
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        $data = Input::except(array('q', 'icon_file'));
        $data['icon_url'] = Config::get('images.image_path') . $file->getClientOriginalName();

        return $this->iconRepository->create($data);
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
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
