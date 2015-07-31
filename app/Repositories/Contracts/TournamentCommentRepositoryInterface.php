<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/07/2015
 * Time: 2:45 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentCommentRepositoryInterface
{
    public function getCommentsForTournament($tournament, $limit = 50);
}