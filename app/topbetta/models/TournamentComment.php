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
	
	public static function getTournamentCommentListByTournamentId( $tournamentId, $limit = 50, $dir = false, $commentId = false )
	{

		$query = TournamentComment::where('tournament_id', '=', $tournamentId)
		            ->join('tbdb_users', 'tbdb_users.id', '=', 'tbdb_tournament_comment.user_id')
		            ->select('tbdb_tournament_comment.*', 'tbdb_users.username')
		            ->orderBy('tbdb_tournament_comment.id', 'desc')
		            ->take($limit);		            

		if ($dir == 'after' && $commentId) {
			$query->where('tbdb_tournament_comment.id', '>', $commentId);
		} else if ($dir == 'before' && $commentId) {
			$query->where('tbdb_tournament_comment.id', '<', $commentId);
		}            

		return $query->get();           
	}    

	/**
	 * only let them post comments for 2 days after the tournament end date
	 * @param  int  $tournamentId Tournament ID
	 * @return boolean               
	 */
	public static function isTournamentCommentingAllowed($tournamentId) {
		$tournament = \TopBetta\Tournament::find($tournamentId);
		$tournamentEndDate = date_create($tournament['end_date']);
		$diff = $tournamentEndDate->diff(date_create("now"))->format("%d");	

		return ($diff >= 2) ? false : true;

	}
}