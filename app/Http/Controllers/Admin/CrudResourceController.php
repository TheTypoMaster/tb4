<?php namespace TopBetta\Http\Controllers\Admin;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/03/2015
 * Time: 4:05 PM
 */

use Request;
use Redirect;
use Input;
use View;
use App;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Icons\IconService;


/**
 * Handles CRUD CMS operations for resources
 * Implementing controllers should make sure to provided values for all variables
 * Class CrudResourceController
 * @package TopBetta\admin\controllers
 */
abstract class CrudResourceController extends Controller {

    /**
     * Name of the resource repository
     * @var $repositoryName
     */
    protected $repositoryName;

    /**
     * The resource repository retrieved from the IoC container
     * @var $repository
     */
    protected $repository;

    /**
     * @var IconService
     */
    protected $iconService;

    /**
     * The type of icons to load for the resource
     * @var
     */
    protected $iconType;

    /**
     * The name of the resource for display purposes
     * @var String
     */
    protected $modelName;

    /**
     * Any default fields to exclude for a resource
     * @var array
     */
    protected $excludedFields = array();

    // --- ROUTES --- //
    protected $indexRoute;

    protected $editRoute;

    protected $createRoute;

    protected $storeRoute;

    protected $updateRoute;

    protected $deleteRoute;

    protected $createChildRoute = null;

    // --- VIEWS --- //
    protected $indexView;

    protected $createView;

    protected $editView;

    /**
     * @param IconService $iconService
     */
    public function __construct(IconService $iconService)
    {
        $this->iconService = $iconService;
        $this->repository = App::make($this->repositoryName);
    }


    /**
     * Display a listing of the resource.
     *
     * @param array $relations
     * @param array $extraData
     * @return Response
     */
    public function index($relations = array(), $extraData = array())
    {
        $search = Request::get('q', '');

        $relations[] = 'icon';

        if ($search) {
            $modelCollection = $this->repository->search($search, $relations);
        } else {
            $modelCollection = $this->repository->findAllPaginated($relations);
        }

        //the data array for display
        $data = array(
            "modelName"       => $this->modelName,
            "modelCollection" => $modelCollection,
            "createRoute"     => $this->createRoute,
            "editRoute"       => $this->editRoute,
            "deleteRoute"     => $this->deleteRoute,
            "extraFields"     => $extraData,
            "search"          => $search,
            "excludedFields"  => $this->excludedFields,
            'createChildRoute' => $this->createChildRoute,
        );

        return View::make($this->indexView, compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param array $extraData
     * @return Response
     */
    public function create($extraData = array())
    {
        $search = Input::get('q', '');

        $allIcons = Input::get("all_icons", false);

        //get the icons
        $icons = $this->iconService->getIcons($allIcons ? null : $this->iconType);

        $data = array(
            "model"       => null,
            "modelName"   => $this->modelName,
            "returnRoute" => $this->indexRoute,
            "formAction" => array("method" => "POST", "route"  => array($this->storeRoute, "q" => $search)),
            "extraFields" => $extraData,
            "icons"       => $icons,
            "search"      => $search,
            "excludedFields"  => $this->excludedFields,
        );

        return View::make($this->createView, compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $data = Input::except('q');
        $newModel = $this->repository->updateOrCreate($data);

        return Redirect::route($this->indexRoute, array($newModel['id']))
            ->with('flash_message', 'Saved!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param array $extraData
     * @return Response
     */
    public function edit($id, $extraData = array())
    {
        $search = Input::get('q', '');

        $allIcons = Input::get("all_icons", false);

        //get the icons
        $icons = $this->iconService->getIcons($allIcons ? null : $this->iconType);

        $data = array(
            "model"       => $this->repository->find($id),
            "modelName"   => $this->modelName,
            "returnRoute" => $this->indexRoute,
            "formAction"  => array("method" => "PUT", "route"  => array($this->updateRoute, $id, "q" => $search)),
            "extraFields" => $extraData,
            "icons"       => $icons,
            'search'      => $search,
            "excludedFields"  => $this->excludedFields,
        );

        return View::make($this->editView, compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //Get the search string for filtering when redirecting
        $search = Input::get("q", '');

        $data = Input::except('q');
        $this->repository->updateWithId($id, $data);

        return Redirect::route($this->indexRoute, array($id, "q"=>$search))
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
        $search = Input::get('q', '');

        $model = $this->repository->find($id);

        $model->delete();

        return Redirect::route($this->indexRoute, array("q" => $search));
    }
}