<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('sourcecoast.utilities');

class JFBConnectViewLoginRegister extends JView
{

    function display($tpl = null)
    {
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $fbUserId = $jfbcLibrary->getFbUserId();
        $fbUserProfile = $jfbcLibrary->_getUserName($fbUserId);
        $configModel = $jfbcLibrary->getConfigModel();

        if ($fbUserId == null)
        {
            $app = JFactory::getApplication();
            $app->redirect('index.php');
        }

        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('jfbcprofiles');
        $profileFields = $app->triggerEvent('jfbcProfilesOnShowRegisterForm');

        
            JHTML::_('behavior.mootools');
            $this->assignRef('fbUserEmail', $this->_getDisplayEmail($fbUserProfile['email']));
         //SC15

         //SC16

        //Check for form validation from each of the plugins
        $areProfilesValidating = $app->triggerEvent('jfbcProfilesAddFormValidation');
        $defaultValidationNeeded = true;
        foreach ($areProfilesValidating as $hasDoneValidation)
        {
            if ($hasDoneValidation == true)
            {
                $defaultValidationNeeded = false;
                break;
            }
        }

        //Check to see if JLinked is installed
        $jLinkedLoginButton = "";
        if (SCSocialUtilities::isJLinkedInstalled())
        {
            require_once(JPATH_ROOT . DS . 'components' . DS . 'com_jlinked' . DS . 'libraries' . DS . 'linkedin.php');
            $jLinkedLibrary =& JLinkedApiLibrary::getInstance();

            $lang = JFactory::getLanguage();
            $lang->load('com_jlinked');
            $loginText = JText::_('COM_JLINKED_LOGIN_USING_LINKEDIN');

            $jLinkedLoginButton = '<link rel="stylesheet" href="components/com_jlinked/assets/jlinked.css" type="text/css" />';
            $jLinkedLoginButton .= '<div class="jLinkedLogin"><a href="' . $jLinkedLibrary->getLoginURL() . '"><span class="jlinkedButton"></span><span class="jlinkedLoginButton">' . $loginText . '</span></a></div>';
        }

        // Setup the view appearance
        // TODO: Make the addStyleSheet into a Utilities function to be used elsewhere.
        $displayType = $configModel->getSetting('registration_display_mode');;
        $css = JPath::find($this->_path['template'], 'loginregister.css');
        $css = str_replace(JPATH_ROOT.DS, JURI::base(), $css);
        $doc = JFactory::getDocument();
        $doc->addStyleSheet($css);

        // Set the session bit to check for a new login on next page load
        SCSocialUtilities::setJFBCNewMappingEnabled();

        // Get previously filled in values
        $this->_getLoginRegisterPostData();

        $this->assignRef('fbUserId', $fbUserId);
        $this->assignRef('fbUserProfile', $fbUserProfile);
        $this->assignRef('configModel', $configModel);
        $this->assignRef('profileFields', $profileFields);
        $this->assignRef('jLinkedLoginButton', $jLinkedLoginButton);
        $this->assignRef('defaultValidationNeeded', $defaultValidationNeeded);
        $this->assignRef('displayType', $displayType);

        parent::display($tpl);
    }

    /**
     * Check passed in email to see if it's already in Joomla
     * If so, return blank, forcing the user to input an email address (and getting validation error if using the same)
     * If not, pre-populate the form with the user's FB address
     * @param string $email Users Facebook email address
     * @return string Email value that will be shown on registration form
     */
    function _getDisplayEmail($email)
    {
        $dbo = JFactory::getDBO();
        $query = "SELECT id FROM #__users WHERE email=" . $dbo->quote($email);
        $dbo->setQuery($query);
        $jEmail = $dbo->loadResult();
        if ($jEmail != null)
            return "";
        else
            return $email;
    }

    function _getLoginRegisterPostData()
    {
        $session = JFactory::getSession();
        $postData = $session->get('postDataLoginRegister', array());

        $this->assignRef('postData', $postData);
        $this->assignRef('postDataUsername', $postData['username']);
    }
}
