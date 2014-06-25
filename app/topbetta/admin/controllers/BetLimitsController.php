<?php

namespace TopBetta\admin\controllers;

use BetLimitType as BetLimit;
use View;

class BetLimitsController extends \BaseController {

    /**
     * Betlimit Repository
     *
     * @var Betlimit
     */
    protected $betlimit;

    public function __construct(Betlimit $betlimit)
    {
        $this->betlimit = $betlimit;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $betlimits = $this->betlimit->all();

        return View::make('admin::betlimits.types.index', compact('betlimits'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return View::make('betlimits.types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Input::all();
        $validation = Validator::make($input, Betlimit::$rules);

        if ($validation->passes())
        {
            $this->betlimit->create($input);

            return Redirect::route('betlimits.index');
        }

        return Redirect::route('betlimits.create')
            ->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $betlimit = $this->betlimit->findOrFail($id);

        return View::make('betlimits.types.show', compact('betlimit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $betlimit = $this->betlimit->find($id);

        if (is_null($betlimit))
        {
            return Redirect::route('betlimits.index');
        }

        return View::make('betlimits.types.edit', compact('betlimit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $input = array_except(Input::all(), '_method');
        $validation = Validator::make($input, Betlimit::$rules);

        if ($validation->passes())
        {
            $betlimit = $this->betlimit->find($id);
            $betlimit->update($input);

            return Redirect::route('betlimits.show', $id);
        }

        return Redirect::route('betlimits.edit', $id)
            ->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->betlimit->find($id)->delete();

        return Redirect::route('betlimits.index');
    }

}