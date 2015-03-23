<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/03/2015
 * Time: 4:05 PM
 */

namespace TopBetta\admin\controllers;


class CrudResourceController {


    protected $repository;

    protected $iconRepository;

    protected $indexView;

    protected $editView;

    protected $createView;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $search = Request::get('q', '');
        if ($search) {
            $sports = $this->repository->search($search);
        } else {
            $sports = $this->repository->paginated();
        }

        return View::make($this->indexView, compact('sports', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $icons = $this->
        return View::make($this->createView);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $data = Input::only('name', 'description');
        $newModel = $this->sportsrepo->updateOrCreate($data);

        return Redirect::route('admin.sports.index', array($newModel['id']))
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
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //get search query so we remain filtered when redirecting after editing
        $search = Input::get("q", '');
        $sport = $this->sportsrepo->find($id);

        $icons = IconModel::all();
        if (is_null($sport)) {
            // TODO: flash message user not found
            return Redirect::route('admin.sports.index', array("q" => $search));
        }

        return View::make('admin::eventdata.sports.edit', compact('sport', 'search', 'icons'));
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

        $data = Input::only('name', 'description');
        $this->sportsrepo->updateWithId($id, $data);

        return Redirect::route('admin.sports.index', array($id, "q"=>$search))
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