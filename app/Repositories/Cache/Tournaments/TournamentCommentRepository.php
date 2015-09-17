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

        if (!$comments->count()) {
            return $this->repository->getCommentsForTournament($tournament, $limit);
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

        $this->put($key, $comments->toArray(), $this->getCollectionCacheTime($collectionKey, $resource));

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

}