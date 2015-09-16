<?php

namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\TournamentCommentModel;
use TopBetta\Services\Tournaments\TournamentCommentService;
use Sentry;
use TopBetta\Services\Tournaments\TournamentService;

class TournamentCommentsController extends Controller
{

    public function __construct(TournamentCommentService $commentService, TournamentService $tournamentService) {
        $this->commentService = $commentService;
        $this->tournamentService = $tournamentService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $comments = $this->commentService->getAllComments();
        $tournament_list = $this->tournamentService->getTournamentsFromToday();
//        dd($tournament_list);
        return view('admin.tournaments.comments.index')->with(['comments' => $comments, 'tournament_list' => $tournament_list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return 'new comment';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store()
    {
        $comment = new TournamentCommentModel();
        $comment::create(['tournament_id' => Input::get('tournament'),
                          'user_id' => Auth::user()->id,
                          'comment' => Input::get('new_comment'),
                          'visible' => 1]);

        //get tournament list


        return redirect()->action('Admin\TournamentCommentsController@index');
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
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
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
        $comment = TournamentCommentModel::findOrFail($id);
        $comment->delete();

        return redirect()->action('Admin\TournamentCommentsController@index');
    }

    public function filter() {

    }
}
