<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

jimport('mobileactive.model.super');
/**
 * Tournament Payout Final Model
 */
class TournamentModelTournamentPayoutType extends SuperModel
{

	protected $_table_name = '#__tournament_payout_type';

	protected $_member_list = array('id' => array('type' => self::TYPE_INTEGER, 'primary' => true),
									'keyword' => array('type' => self::TYPE_STRING),
									'name' => array('type' => self::TYPE_STRING),
									'description'=> array('type' => self::TYPE_STRING));
}