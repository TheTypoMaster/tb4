<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Contracts\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Tournaments\TournamentCommentService;
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

    public function __construct(TournamentCommentService $commentService, ApiResponse $response)
    {
        $this->commentService = $commentService;
        $this->response = $response;
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

        return $this->response->success("Comment saved");
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
