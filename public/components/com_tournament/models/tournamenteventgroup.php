<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
require_once 'eventgroup.php';

class TournamentModelTournamentEventGroup extends TournamentModelEventGroup
{
	/**
	 * Get Tournament event by name
	 * @param string $name
	 */
	public function getActiveTournamentEventGroupListByCompetitonID($competition_id = NULL)
	{
		$db =& $this->getDBO();
		$query = '
			SELECT
				eg.id,
				eg.external_event_group_id,
				eg.wagering_api_id,
				eg.name,
				eg.tournament_competition_id,
				eg.start_date,
				eg.display_flag,
				eg.created_date,
				eg.updated_date,
				eg.state
			FROM
				' . $db->nameQuote('#__event_group') . ' AS eg
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.event_group_id = eg.id
			WHERE
				eg.tournament_competition_id = ' . $db->quote($competition_id) . ' 
			AND
				(
					t.betting_closed_date > NOW()
				OR
					t.betting_closed_date IS NULL AND t.end_date > NOW()
				)
			GROUP BY
				eg.id
			';
		$db->setQuery($query);
		return $db->loadObjectList('id');
	}
}