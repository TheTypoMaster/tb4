<?php defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
$defaultAdminTemplate = modMyAdminTemplateHelper::getDefaultAdminTemplate();
$adminTemplates = modMyAdminTemplateHelper::getAdminTemplates();
?>

<script type="text/javascript">
	function updateAdminTemplate()
	{
		var admintemplate=document.getElementById('myadmintemplate_selection');
		var myXHR=new XHR({method:'post', onSuccess:adminTemplateShowUpdateSuccess}).send('index.php', 'option=com_templates&task=publish&<?php echo JUtility::getToken(); ?>=1&cid[]='+admintemplate.value+'&client=1');
		adminTemplateColorSelectBox(true);
	}

	function adminTemplateShowUpdateSuccess(req)
	{
		setTimeout('adminTemplateColorSelectBox(false)', 1000);
		document.location.reload();
	}

	function adminTemplateColorSelectBox(set)
	{
		var admintemplate=document.getElementById('myadmintemplate_selection');
		if (set)
			admintemplate.setAttribute("style", "border: 3px solid #3AC521");
		else
			admintemplate.removeAttribute("style");
	}
</script>

<select id="myadmintemplate_selection" onChange="javascript:updateAdminTemplate()">
<!--option>Select Admin Template</option-->
<?php 
foreach ($adminTemplates as $adminTemplate) {
	$dir = strtolower($adminTemplate->directory);
	echo '<option value="'.$dir.'" '.(($defaultAdminTemplate == $dir) ? 'SELECTED="SELECTED"' : "").'>'.$adminTemplate->name.'</option>';

}
?>
</select>
</p>

