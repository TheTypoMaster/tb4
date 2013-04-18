<?php
/**
 * @package        JFBConnect/JLinked
 * @copyright (C) 2011-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

echo $helper->getSocialAvatar($registerType, $profileLink, $user);

if ($params->get('showGreeting'))
{
    if ($params->get('greetingName') == 0)
        $name = $user->get('username');
    else
        $name = $user->get('name');
    echo '<div>' . JText::sprintf('MOD_SCLOGIN_WELCOME', $name) . '</div>';
}

if ($params->get('showLogoutButton'))
{ ?>
    <div class="logout-button">
        <?php echo $helper->getLogoutButton(); ?>
    </div>
    <br/>
<?php
}

if ($params->get('showConnectButton'))
{
    $reconnectButtons = $helper->getReconnectButtons();
    echo $reconnectButtons;
}

echo $helper->getPoweredByLink();
?>
