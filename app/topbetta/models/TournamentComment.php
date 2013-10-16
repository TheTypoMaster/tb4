<?php
namespace TopBetta;

class TournamentComment extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();

    protected $table = 'tbdb_tournament_comment';

	/**
	 * Get a single tournaments comments
	 *
	 * @param integer $id
	 * @return object
	 */
	
	public static function getTournamentCommentListByTournamentId( $tournamentId )
	{

		return TournamentComment::where('tournament_id', '=', $tournamentId)
		            ->join('tbdb_users', 'tbdb_users.id', '=', 'tbdb_tournament_comment.user_id')
		            ->select('tbdb_tournament_comment.*', 'tbdb_users.username')
		            ->orderBy('tbdb_tournament_comment.created_at', 'asc')
		            ->get();
	}    
}