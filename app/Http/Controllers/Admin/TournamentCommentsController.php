<?php

namespace TopBetta\Http\Controllers\Admin;

use Carbon\Carbon;
//use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\TournamentCommentModel;
use TopBetta\Repositories\Contracts\TournamentCommentRepositoryInterface;
use TopBetta\Services\Tournaments\TournamentCommentService;
use Sentry;
use TopBetta\Services\Tournaments\TournamentService;
use Request;

class TournamentCommentsController extends Controller
{

    public function __construct(TournamentCommentService $commentService, TournamentService $tournamentService, TournamentCommentRepositoryInterface $tournamentCommentRepository) {
        $this->commentService = $commentService;
        $this->tournamentService = $tournamentService;
        $this->tournamentCommentRepository = $tournamentCommentRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(Input::get('flag')) {

            //get search query
            $tournament_id = Input::get('tournament_id');
            $username = Input::get('username');
            $visibility = Input::get('visible');
            $query = array('flag' => '1', 'tournament_id' => $tournament_id, 'username' => $username, 'visibility' => $visibility);
            $comments_with_pagination = $this->commentService->searchComments($tournament_id, $username, $visibility);

        } else {
            $query = array();
            $comments_with_pagination = $this->commentService->getAllComments();
        }
        $comments = $comments_with_pagination['comment_list'];
        $pagination = $comments_with_pagination['pagination'];
        $tournament_list = $this->tournamentService->getTournamentsFromToday();
        return view('admin.tournaments.comments.index')->with(['comments' => $comments,
                                                               'tournament_list' => $tournament_list,
                                                               'pagination' => $pagination,
                                                               'query' => $query]);
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
        $this->tournamentCommentRepository->create(['tournament_id' => Input::get('tournament'),
            'user_id' => Auth::user()->id,
            'comment' => Input::get('new_comment'),
            'created_date' => Carbon::now(),
            'visible' => 1]);


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
        $comment = $this->commentService->getCommentById($id);
        return view('admin.tournaments.comments.edit')->with('comment', $comment);
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

        $comment = TournamentCommentModel::findOrFail($id);
        $visible = '';
        if($comment->visible == 0) {
            $visible = 1;
        } else if($comment->visible == 1) {
            $visible = 0;
        }

        $this->tournamentCommentRepository->update($comment,['visible' => $visible]);

        return redirect()->action('Admin\TournamentCommentsController@index');
    }

    public function updateComment(Request $request, $id)
    {
        $comment = $this->commentService->getCommentById($id);
        $this->tournamentCommentRepository->update($comment,['comment' => Input::get('comment')]);

        return redirect()->action('Admin\TournamentCommentsController@index');
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
        $this->tournamentCommentRepository->delete($comment);

        return redirect()->action('Admin\TournamentCommentsController@index');
    }

    public function filter() {

    }
}
