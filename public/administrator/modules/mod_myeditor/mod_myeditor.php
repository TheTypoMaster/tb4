<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');

$editors = modMyEditorHelper::getEditors();
require(JModuleHelper::getLayoutPath('mod_myeditor'));

?>
