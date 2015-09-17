<?php

namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Redirect;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\TournamentGroupRepositoryInterface;
use View;

class TournamentGroupController extends Controller
{

    /**
     * @var TournamentGroupRepositoryInterface
     */
    private $tournamentGroupRepository;

    public function __construct(TournamentGroupRepositoryInterface $tournamentGroupRepository)
    {
        $this->tournamentGroupRepository = $tournamentGroupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('q', '');

        if($search) {
            $groups = $this->tournamentGroupRepository->search($search);
        } else {
            $groups = $this->tournamentGroupRepository->findAllPaginated();
        }

        return View::make('admin.tournaments.groups.index', compact('groups', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $search = $request->get('q', '');

        return View::make('admin.tournaments.groups.create', compact('search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $search = $request->get('q', '');

        $data = $request->only(array(
            'group_name', 'description', 'tournament_group_icon', 'ordering'
        ));

        $data['ordering'] = $data['ordering'] ? : null;

        $group = $this->tournamentGroupRepository->create($data);

        return Redirect::route('admin.tournament-groups.index', compact('search'))
            ->with(array('flash_message' => 'Success'));
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
     * @param  int $id
     * @param Request $request
     * @return Response
     */
    public function edit($id, Request $request)
    {
        $search = $request->get('q' ,'');

        $group = $this->tournamentGroupRepository->find($id);

        return View::make('admin.tournaments.groups.edit', compact('search', 'group'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $search = $request->get('q', '');

        $data = $request->only(array(
            'group_name', 'description', 'tournament_group_icon', 'ordering'
        ));

        $data['ordering'] = $data['ordering'] ? : null;

        $this->tournamentGroupRepository->updateWithId($id, $data);

        return Redirect::route('admin.tournament-groups.index', compact('search'))
            ->with(array('flash_message' => 'Success'));
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
