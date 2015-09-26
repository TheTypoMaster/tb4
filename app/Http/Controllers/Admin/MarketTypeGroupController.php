<?php

namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Markets\MarketTypeGroupService;

class MarketTypeGroupController extends Controller
{

    public function __construct(MarketTypeGroupService $marketTypeGroupService) {
        $this->marketTypeGroupService = $marketTypeGroupService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $marketTypeGroups = $this->marketTypeGroupService->getMarketTypeGroups();
        return view('admin.marketgroups.index')->with('marketTypeGroups', $marketTypeGroups);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.marketgroups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {


        $this->validate($request, [
            'market_type_group_name' => 'required|max:255'
        ]);

        $groups = array('market_type_group_name' => Input::get('market_type_group_name'),
                        'market_type_group_description' => Input::get('market_type_group_description'),
                        'icon_id' => Input::get('icon_id'));

        $this->marketTypeGroupService->createMarketTypeGroup($groups);

        return redirect()->action('Admin\MarketTypeGroupController@index');
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
        $market_type_group = $this->marketTypeGroupService->getGroupById($id);
        return view('admin.marketgroups.edit')->with(['market_type_group' => $market_type_group]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $market_type_gruop = array('market_type_group_name' => Input::get('market_type_group_name'),
                                   'market_type_group_description' => Input::get('market_type_group_description'),
                                   'icon_id' => Input::get('icon_id'));
        $this->marketTypeGroupService->updateMarketTypeGroup($id, $market_type_gruop);
        return redirect()->action('Admin\MarketTypeGroupController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->marketTypeGroupService->deleteMarketTypeGroup($id);
        return redirect()->action('Admin\MarketTypeGroupController@index');
    }
}
