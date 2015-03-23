<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/03/2015
 * Time: 4:05 PM
 */

namespace TopBetta\admin\controllers;

use Request;
use Redirect;
use Input;
use View;
use App;
use TopBetta\Services\Icons\IconService;

class CrudResourceController extends \BaseController{

    protected $repositoryName;

    protected $repository;

    protected $iconService;

    protected $iconType;

    protected $modelName;

    protected $indexRoute;

    protected $editRoute;

    protected $createRoute;

    protected $storeRoute;

    protected $updateRoute;

    protected $deleteRoute;

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
     * @param array $extraData
     * @return Response
     */
    public function index($extraData = array())
    {
        $search = Request::get('q', '');
        if ($search) {
            $modelCollection = $this->repository->search($search);
        } else {
            $modelCollection = $this->repository->paginated();
        }

        $data = array(
            "modelName"       => $this->modelName,
            "modelCollection" => $modelCollection,
            "createRoute"     => $this->createRoute,
            "editRoute"       => $this->editRoute,
            "deleteRoute"     => $this->deleteRoute,
            "extraFields"     => $extraData,
            "search"          => $search,
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

        $icons = $this->iconService->getIcons($allIcons ? null : $this->iconType);

        $data = array(
            "model"       => null,
            "modelName"   => $this->modelName,
            "returnRoute" => $this->indexRoute,
            "formAction" => array("method" => "POST", "route"  => array($this->storeRoute, "q" => $search)),
            "extraFields" => $extraData,
            "icons"       => $icons,
            "search"      => $search
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

        $icons = $this->iconService->getIcons($allIcons ? null : $this->iconType);

        $data = array(
            "model"       => $this->repository->find($id),
            "modelName"   => $this->modelName,
            "returnRoute" => $this->indexRoute,
            "formAction"  => array("method" => "PUT", "route"  => array($this->updateRoute, $id, "q" => $search)),
            "extraFields" => $extraData,
            "icons"       => $icons,
            'search'      => $search
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