<?php

namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\TournamentPrizeFormatRepositoryInterface;

class PrizeFormatController extends Controller
{

    public function __construct(TournamentPrizeFormatRepositoryInterface $tournamentPrizeFormatRepository) {
        $this->tournamentPrizeFormatRepository = $tournamentPrizeFormatRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $prize_formats = $this->tournamentPrizeFormatRepository->getPrizeFormatList();

        return view('admin.tournaments.prize-format.index')->with('prize_formats', $prize_formats);
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
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
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
        $prize_format = $this->tournamentPrizeFormatRepository->getPrizeFormatById($id);

        return view('admin.tournaments.prize-format.edit')->with('prize_format', $prize_format);

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
        $data = array('short_name' => Input::get('short_name'),
                      'icon' => Input::get('icon'));
        $this->tournamentPrizeFormatRepository->update($id, $data);
        return redirect()->action('Admin\PrizeFormatController@index');
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
