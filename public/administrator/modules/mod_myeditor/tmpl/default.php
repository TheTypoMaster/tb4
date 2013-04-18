<?php defined('_JEXEC') or die('Restricted access');
$myEditor = JFactory::getEditor();
$user = JFactory::getUser();
?>

<script type="text/javascript">
	function updateEditor()
	{
		var editor=document.getElementById('myeditor_selection');
		var myXHR=new XHR({method:'post', onSuccess:showUpdateSuccess}).send('index.php', 'option=com_users&task=save&id=<?php echo $user->get('id'); ?>&sendEmail=0&<?php echo JUtility::getToken(); ?>=1&username=<?php echo $user->get('username');?>&params[editor]='+editor.value+'&tmpl=COMPONENT');
		colorSelectBox(true);
	}

	function showUpdateSuccess(req)
	{
		setTimeout('colorSelectBox(false)', 1000);
	}

	function colorSelectBox(set)
	{
		var editor=document.getElementById('myeditor_selection');
		if (set)
			editor.setAttribute("style", "border: 3px solid #3AC521");
		else
			editor.removeAttribute("style");
	}
</script>

<select id="myeditor_selection" onChange="javascript:updateEditor()">
<?php foreach ($editors as $editor)
	if ($myEditor->_name == $editor->element)
		echo '<option value="'.$editor->element.'" SELECTED="SELECTED">'.$editor->text.'</option>';
	else
		echo '<option value="'.$editor->element.'">'.$editor->text.'</option>';
?>
</select>
</p>

