<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Contracts\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\TournamentModel;
use TopBetta\Models\UserModel;
use TopBetta\Resources\Tournaments\CommentResource;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Tournaments\TournamentCommentService;
use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class TournamentCommentController extends Controller
{
    const LOG_PREFIX = "TournamentCommentController";

    /**
     * @var TournamentCommentService
     */
    private $commentService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(TournamentCommentService $commentService, UserAccountService $userService, ApiResponse $response)
    {
        $this->commentService = $commentService;
        $this->response = $response;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $comments = $this->commentService->getComments($request->all());
            $comments = $comments->toArray();

            //change user name to be TopBetta Admin if the user is a super user
            foreach($comments['data'] as $key => $comment) {
                $comment = $this->commentService->getCommentById($comment['id']);
                $user_id = $comment->user_id;
                $user = $this->userService->getUser($user_id);
//                if($user->usertype == 'Super Administrator') {
//                    $comments['data'][$key]['username'] = 'TopBetta Admin';
//                }

                if($user->permissions) {

                    if($user->permissions['superuser'] == 1) {
                        $comments['data'][$key]['username'] = 'TopBetta Admin';
                    } else {
                        $comments['data'][$key]['username'] = $user->name;
                    }

                } else {
                    $comments['data'][$key]['username'] =  $user->name;
                }
            }

        } catch (\InvalidArgumentException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error(self::LOG_PREFIX . ': ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }
        return $this->response->success($comments['data'], 200, array_except($comments, 'data'));
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
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $comment = $this->commentService->storeComment(\Auth::user(), $request->all());
        } catch (UnauthorizedException $e) {
            return $this->response->failed($e->getMessage(), 401);
        } catch (ValidationException $e) {
            return $this->response->failed($e->getErrors()->first(), 400);
        } catch (ModelNotFoundException $e) {
            return $this->response->failed($e->getMessage(), 404);
        } catch (\Exception $e) {
            \Log::error(self::LOG_PREFIX . ': ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success((new CommentResource($comment))->toArray());
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
     * @param  int  $id
     * @return Response
     */
    public function update($id)
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
        //
    }
}
