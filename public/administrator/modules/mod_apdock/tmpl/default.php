<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$fishEyeId = "apdockfisheye";
$user = &JFactory::getUser();
?>
<script type="text/javascript" src="modules/mod_apdock/tmpl/ifisheye.js"></script>
<script type="text/javascript">
	window.addEvent("domready", function() {
		new iFishEye({
			container: $("<?php print $fishEyeId; ?>"),
			norm: "L2",
			blankPath: "modules/mod_apdock/tmpl/images/blank.gif"
		});
	});
</script>

<style type="text/css">
#<?php print $fishEyeId; ?> {
position: fixed;
bottom: 0;
text-align: center;
width:100%;
background:transparent url('templates/adminpraise2/images/icons/dock/dock-bg.png') repeat-x 0 100%;
}
#<?php print $fishEyeId; ?> table {
margin: 0 auto;
border-collapse:collapse;
}
#<?php print $fishEyeId; ?> td {
text-align: center;
vertical-align: bottom;
}
#<?php print $fishEyeId; ?> a {color:#FFF;text-decoration: none;cursor:pointer;}
#<?php print $fishEyeId; ?> span {
display: block;
margin-bottom: -15px;
padding:1px 1px;
font-weight: bold;
font-size: 105%;
background:#AAA;
border:1px solid #666;
cursor:pointer;
filter:alpha(opacity=50);
-moz-opacity: 0.5;
opacity: 0.5;
}
</style>

<div id="<?php print $fishEyeId; ?>">
	<table>
		<tr>
			<td>
				<a href="index.php">
					<span class="iFishEyeCaption"><?php echo JText::_( 'HOME' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/go-home.png" alt="Home" />
				</a>
			</td>
			<?php if($user->get('gid') > 24) { ?>
			<td>
				<a href="index.php?option=com_config">
					<span class="iFishEyeCaption"><?php echo JText::_( 'GLOBALS' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/config.png" alt="Globals" />
				</a>
			</td>
			<td>
				<a href="<?php print AdminPraiseHelper::getAdminParamsLink(); ?>">
					<span class="iFishEyeCaption"><?php echo JText::_( 'ADMIN PARAMS' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/gear.png" alt="Admin Params" />
				</a>
			</td>
			<?php } ?>
<?php
			$link = AdminPraiseHelper::getFileBrowserLink();
			if(($link != null) && ($user->get('gid') > 23))
			{
?>
			<td>
				<a href="<?php print $link; ?>">
					<span class="iFishEyeCaption"><?php echo JText::_( 'FILE EXPLORER' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/files.png" alt="Files" />
				</a>
			</td>
<?php
			}
?>
			<td>
				<a href="index.php?option=com_admin&task=sysinfo">
					<span class="iFishEyeCaption"><?php echo JText::_( 'SYSTEM INFO' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/info.png" alt="System Info" />
				</a>
			</td>
			<td>
				<a href="index.php?option=com_content&task=add">
					<span class="iFishEyeCaption"><?php echo JText::_( 'NEW ARTICLE' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/newart.png" alt="New Article" />
				</a>
			</td>
			<td>
				<a href="index.php?option=com_menus">
					<span class="iFishEyeCaption"><?php echo JText::_( 'MENUS' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/menus.png" alt="Menus" />
				</a>
			</td>
			<td>
				<a href="index.php?option=com_sections&scope=content">
					<span class="iFishEyeCaption"><?php echo JText::_( 'SECTIONS' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/section.png" alt="Sections" />
				</a>
			</td>
			<td>
				<a href="index.php?option=com_categories&scope=content">
					<span class="iFishEyeCaption"><?php echo JText::_( 'CATEGORIES' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/category.png" alt="Categories" />
				</a>
			</td>
			<td>
				<a href="index.php?option=com_content">
					<span class="iFishEyeCaption"><?php echo JText::_( 'ARTICLES' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/articles.png" alt="Articles" />
				</a>
			</td>
			<td>
				<a href="index.php?ap_task=list_components">
					<span class="iFishEyeCaption"><?php echo JText::_( 'COMPONENTS' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/extensions.png" alt="Components" />
				</a>
			</td>
			<?php if($user->get('gid') > 24) { ?>
			<td>
				<a href="index.php?option=com_modules">
					<span class="iFishEyeCaption"><?php echo JText::_( 'MODULES' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/module.png" alt="Modules" />
				</a>
			</td>
			<td>
				<a href="index.php?option=com_plugins">
					<span class="iFishEyeCaption"><?php echo JText::_( 'PLUGINS' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/plugin.png" alt="Plugins" />
				</a>
			</td>
			<?php } ?>
			<?php if($user->get('gid') > 23) { ?>
			<td>
				<a href="index.php?option=com_installer">
					<span class="iFishEyeCaption"><?php echo JText::_( 'INSTALLER' );?></span><br />
					<img class="iFishEyeImg" src="templates/adminpraise2/images/icons/dock/installer.png" alt="Installer" />
				</a>
			</td>
			<?php } ?>
		</tr>
	</table>
</div>
