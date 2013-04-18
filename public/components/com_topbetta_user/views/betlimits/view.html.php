<?php
/**
 * @version		$Id: view.html.php 10752 2008-08-23 01:53:31Z eddieajau $
 * @package		Joomla
 * @subpackage	User
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Users component
 *
 * @package		Joomla
 * @subpackage	User
 * @since		1.5
 */
class topbettaUserViewBetlimits extends JView
{
	/**
	 * Display function
	 *
	 * @since 1.5
	 */
	function display($tpl = null) {
		$no_limit = true;
		$bet_limit = null;
		
		if ($this->user->bet_limit >= 0) {
			$no_limit	= false;
			$bet_limit	= bcdiv($this->user->bet_limit, 100, 2);
		}
		
		$requested_limit_change = null;
		if ($this->requested_date) {
			$requested_limit_change = 'The request to raise your loss limit to ' . ($this->user->requested_bet_limit == -1 ? '"No Limit"' : ('$' . bcdiv($this->user->requested_bet_limit, 100, 2))) . ' was sent on ' . date('d/m/Y', strtotime($this->requested_date));
		}
		
		$this->assign('no_limit', $no_limit);
		$this->assign('bet_limit', $bet_limit);
		$this->assign('requested_limit_change', $requested_limit_change);
		
		$config =& JFactory::getConfig();
		$this->assign('time_zone', $config->getValue('config.time_zone'));
		
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TopBetta - Bet Limits') );
		$document->addStyleSheet('components/com_topbetta_user/assets/betlimit.css');
		$document->addScript('components/com_topbetta_user/assets/betlimit.js');
		
		parent::display($tpl);
	}
}
