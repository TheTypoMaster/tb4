<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/09/2015
 * Time: 10:30 AM
 */

namespace TopBetta\Repositories\Cache\Tournaments;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Jobs\Pusher\Tournaments\CommentSocketUpdate;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\TournamentCommentRepositoryInterface;
use TopBetta\Repositories\DbTournamentCommentRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\PaginatedEloquentResourceCollection;

class TournamentCommentRepository extends CachedResourceRepository implements TournamentCommentRepositoryInterface {

    const CACHE_KEY_PREFIX = 'tournament_comment_';

    const COLLECTION_TOURNAMENT_COMMENT= 0;

    protected $resourceClass = 'TopBetta\Resources\Tournaments\CommentResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("tournaments", "tournament_comment");

    protected $storeIndividualResource = false;

    protected $collectionKeys = array(
        self::COLLECTION_TOURNAMENT_COMMENT
    );


    public function __construct(DbTournamentCommentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getCommentsForTournament($tournament, $limit = 50)
    {
        $comments = $this->getCollection($this->cachePrefix . $tournament);

        if (!$comments) {
            $comments = $this->repository->getAllVisibleTournamentComments($tournament);
            $this->insertComments($comments->first()->tournament, $comments);
            $comments = new EloquentResourceCollection($comments, $this->resourceClass);
        }

        $page = \Request::get('page', 0);

        return PaginatedEloquentResourceCollection::makeFromEloquentResourceCollection($comments, $limit, $page);

    }

    public function addToCollection($resource, $collectionKey, $resourceClass = null)
    {
        $key = $this->getCollectionCacheKey($collectionKey, $resource);

        $comments = $this->getCollection($key);

        if (!$comments) {
            $comments = new EloquentResourceCollection(new Collection(), $this->resourceClass);
        }

        $comments->prepend($resource);

        $this->put($key, $commentsArray = $comments->toArray(), $this->getCollectionCacheTime($collectionKey, $resource));

        \Bus::dispatch(new CommentSocketUpdate($resource->toArray()));
    }

    public function insertComments($tournament, $comments)
    {
        $this->put(
            $this->cachePrefix . $tournament->id,
            (new EloquentResourceCollection($comments, $this->resourceClass))->toArray(),
            Carbon::createFromFormat('Y-m-d H:i:s', $tournament->end_date)->addDays(2)->diffInMinutes()
        );
    }

    public function getCollectionCacheTime($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_TOURNAMENT_COMMENT:
                return Carbon::createFromFormat('Y-m-d H:i:s', $model->getModel()->tournament->end_date)->addDays(2)->diffInMinutes();
        }

        throw new \InvalidArgumentException("Invalid collection key " . $collectionKey);
    }

    public function getCollectionCacheKey($collectionKey, $model)
    {
        switch ($collectionKey) {
            case self::COLLECTION_TOURNAMENT_COMMENT:
                return $this->cachePrefix . $model->getModel()->tournament_id;
        }

        throw new \InvalidArgumentException("Invalid collection key " . $collectionKey);
    }

    /**
     * @param $key
     * @return EloquentResourceCollection
     */
    public function getCollection($key, $resource = null)
    {
        $collection = \Cache::tags($this->tags)->get($key);

        if ($collection) {
            return $this->createCollectionFromArray($collection, $resource);
        }

        return null;
    }

    public function makeCacheResource($model) {

        if($model->visible) {
            parent::makeCacheResource($model);
        }else {
            $this->removeFromCache($model);
        }
        return $model;
    }

    public function removeFromCache($model) {

        $comments = $this->getCollection($this->cachePrefix.$model->tournament_id);
        $comments->forget($model->id);
        $this->put($this->cachePrefix.$model->tourmanet_id, $comments->toArray(), Carbon::createFromFormat('Y-m-d H:i:s', $model->tournament->end_date)->addDays(2)->diffInMinutes());
}

}