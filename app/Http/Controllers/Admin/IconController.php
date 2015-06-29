<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\IconRepositoryInterface;
use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;
use View;
use Input;
use Redirect;
use Config;
use File;

class IconController extends Controller {


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
		$search = Input::get('q', '');

        if($search) {
            $icons = $this->iconRepository->search($search);
        } else {
            $icons = $this->iconRepository->findAllPaginated();
        }

        return View::make('admin.eventdata.icons.index', compact('icons', 'search'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $search = Input::get('q', '');
        //get the icon types
        $iconTypes = $this->iconTypeRepository->findAll();

        return View::make('admin.eventdata.icons.create', compact('iconTypes', 'search'));
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
        $data['icon_url'] = Config::get('images.image_base_url') . $file->getClientOriginalName();

        $this->iconRepository->create($data);

        return Redirect::route("admin.icons.index", array("q" => $search))
            ->with(array("flash_message" => "Icon Saved!"));
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
        $search = Input::get('q', '');
        $icon = $this->iconRepository->find($id);

        $iconTypes = $this->iconTypeRepository->findAll();

        return View::make('admin.eventdata.icons.edit', compact('icon', 'iconTypes', 'search'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $search = Input::get('q', '');
        $data = Input::except('q');

        //update
        $this->iconRepository->updateWithId($id, $data);

        return Redirect::route('admin.icons.index', array("q" => $search))
            ->with(array('flash_message' => 'Icon Saved!'));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $search = Input::get('q', '');

        //get the db record
        $icon = $this->iconRepository->find($id);

        //delete the image
        File::delete(Config::get('images.image_path') . basename($icon->icon_url));

        //delete the DB record
        $icon->delete();

        return Redirect::route('admin.icons.index', array("q" => $search))
            ->with(array('flash_message' => 'Icon Saved!'));
	}


}
