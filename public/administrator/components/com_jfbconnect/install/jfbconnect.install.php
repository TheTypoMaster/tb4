<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Get current version number
$jfbcVersion = "4.2.4"; // Same as the XML version
$finalDBVersion = "4.2.0"; // If database updates occur, increment this number
$libraryVersion = "1.0.5"; // Same as the library XML version

$packages[] = array('name' => 'JFBConnect Authentication Plugin', 'file' => 'plg_authentication_jfbconnectauth_j1.5_v4.2.0.zip');
$packages[] = array('name' => 'JFBConnect User Plugin', 'file' => 'plg_user_jfbconnectuser_j1.5_v4.2.1.zip');
$packages[] = array('name' => 'JFBConnect System Plugin', 'file' => 'plg_system_jfbcsystem_j1.5_v4.2.4.zip');
$packages[] = array('name' => 'JFBConnect Content Plugin', 'file' => 'plg_content_jfbccontent_j1.5_v4.2.1.zip');
$packages[] = array('name' => 'SCLogin / JFBConnect Login', 'file' => 'mod_sclogin_j1.5_v1.1.3.zip');
$sourcecoastLibrary = array('name' => 'SourceCoast Social Library', 'file' => 'lib_sourcecoast_j1.5_v1.0.5.zip');

$packageDirectory = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'install' . DS . 'packages' . DS;
?>

<table>
    <tr>
        <td width="100px"><img
                src="<?php print JURI::root(); ?>/administrator/components/com_jfbconnect/assets/images/jfbconn.png"
                width="100px"></td>
        <td><h2>JFBConnect v<?php echo $jfbcVersion ?></h2></td>
    </tr>
</table>
<h3>Installing...</h3>

<?php
$db = JFactory::getDBO();
$db->debug(0);
// 4.1: Add new access_token column
// 4.2: Add "authorized" column to user_map table
$db->setQuery("SELECT `value` FROM #__jfbconnect_config WHERE `setting` = 'db_version'");
$dbVersion = $db->loadResult();
$now = $db->quote(JFactory::getDate()->toMySQL());
$query = "";
switch ($dbVersion)
{
    case "" :
    case null :
        $query .= "ALTER TABLE `#__jfbconnect_config` ADD UNIQUE (`setting`);";
        $query .= "ALTER TABLE `#__jfbconnect_user_map` ADD COLUMN `access_token` VARCHAR(255) DEFAULT NULL;";
    case '4.1.0' :
        $query .= "ALTER TABLE `#__jfbconnect_user_map` ADD COLUMN `authorized` TINYINT(1) DEFAULT 1;";
        break;
    default :
        break;
}

if ($query)
{
    $db->setQuery($query);
    $db->queryBatch(false);
}

$query = "INSERT INTO `#__jfbconnect_config` SET `setting` = 'db_version', `value` = '" . $finalDBVersion . "'" .
        ", created_at = " . $now . ", updated_at = " . $now .
        " ON DUPLICATE KEY UPDATE `value` = '" . $finalDBVersion . "', `updated_at` = " . $now . ";";
$db->setQuery($query);
$db->query();

jimport('joomla.installer.helper');
jimport('joomla.installer.adapters.plugin');
$installer = new JInstaller();
$installer->setOverwrite(true);

foreach ($packages as $package)
{
    $pkgName = $package['name'];
    $pkgFile = $package['file'];
    $pkg = JInstallerHelper::unpack($packageDirectory . $pkgFile);
    if ($installer->install($pkg['dir']))
    {
        ?>
    <table bgcolor="#E0FFE0" width="100%">
        <tr style="height:30px">
            <td width="50px"><img
                    src="<?php print JURI::root(); ?>/administrator/components/com_jfbconnect/assets/images/icon-16-allow.png"
                    height="20px" width="20px"></td>
            <td><font size="2"><b><?php echo $pkgName; ?> successfully installed.</b></font></td>
        </tr>
    </table>
    <?php
    } else
    {
        ?>
    <table bgcolor="#FFD0D0" width="100%">
        <tr style="height:30px">
            <td width="50px"><img
                    src="<?php print JURI::root(); ?>/administrator/components/com_jfbconnect/assets/images/icon-16-deny.png"
                    height="20px" width="20px"></td>
            <td><font size="2"><b>ERROR: Could not install the <?php echo $pkgName; ?>. Please install
                manually.</b></font></td>
        </tr>
    </table>
    <?php
    }
}


# Install the extension library manually by moving the files
$libraryPath = JPATH_SITE . DS . 'libraries' . DS . 'sourcecoast';
if (!JFolder::exists($libraryPath))
    JFolder::create($libraryPath);

jimport('joomla.filesystem.archive');
$libInstall = JArchive::extract($packageDirectory . $sourcecoastLibrary['file'], $libraryPath);


if ($libInstall)
{
    ?>
<table bgcolor="#E0FFE0" width="100%">
    <tr style="height:30px">
        <td width="50px"><img
                src="<?php print JURI::root(); ?>/administrator/components/com_jfbconnect/assets/images/icon-16-allow.png"
                height="20px" width="20px"></td>
        <td><font size="2"><b><?php echo $sourcecoastLibrary['name']; ?> successfully installed.</b></font>
        </td>
    </tr>
</table>
<?php
} else
{
    ?>
<table bgcolor="#FFD0D0" width="100%">
    <tr style="height:30px">
        <td width="50px"><img
                src="<?php print JURI::root(); ?>/administrator/components/com_jfbconnect/assets/images/icon-16-deny.png"
                height="20px" width="20px"></td>
        <td><font size="2"><b>ERROR: Could not install the SourceCoast Social Library. Please install
            manually.</b></font></td>
    </tr>
</table>
<?php
}
?>
<p style="font-weight:bold; margin-top:20px">To configure and optimize JFBConnect, it's recommended to run Autotune whenever you install or upgrade:</p>
<center><a href="index.php?option=com_jfbconnect&view=autotune" style="background-color:#025A8D;color:#FFFFFF;height:35px;padding:15px 45px;font-weight:bold;font-size:18px;line-height:60px;text-decoration:none;">Run Autotune Now</a></center>
