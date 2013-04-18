<?php
/**
 * Joomla! 1.5 component uc_betman
 *
 * @version $Id: view.html.php 2009-08-07 04:40:27 svn $
 * @author uc-joomla.net
 * @package Joomla
 * @subpackage uc_betman
 * @license Copyright (c) 2009 - All Rights Reserved
 *
 * sports tournament betting component
 *

 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the uc_betman component
 */
class topbettaUserViewMyaccount extends JView {

  function display($tpl = null) {
    $document = & JFactory::getDocument();
    $document->setTitle( JText::_('TopBetta - Account Settings') );

    $model =& $this->getModel('topbettaUser');

    $currentLayout = $this->getLayout();
    if ($currentLayout == 'accountsettings')
    {
      $document->addStyleSheet('components/com_topbetta_user/assets/view.register.css');

      $document->addScript( 'components/com_topbetta_user/assets/formValidation.js' );
      $document->addScript( 'components/com_topbetta_user/assets/pwdmeter.js' );
    }else{
      $document->addStyleSheet('components/com_topbetta_user/assets/myaccount.default.css');
    }
    if ($currentLayout == 'default')
    {
      $todayDate = date( 'Y-m-d', strtotime('today 00:00'));
    }


    parent::display($tpl);
  }
}
?>