<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

function showJbField($form, $name='')
{
	echo '<div class="control-label">';
	echo $form->getLabel($name);
	echo '</div><div class="goals-form-datainput controls">';
	echo $form->getInput($name);
	echo '</div>';
}


JHTML::_('behavior.modal', 'a.modal');
$old = false;

if (isset($this->item->id)) $old=true;
?>
<div id="goals-wrap">
<script type="text/javascript">
function function_exists( function_name ) {
	if (typeof function_name == 'string'){
		return (typeof window[function_name] == 'function');
	} else{
		return (function_name instanceof Function);
	}
}

	Joomla.submitbutton = function(task) {
		if (task != 'plantask.cancel')
		{
			if (function_exists('isValidURLs'))	if (!isValidURLs()) return false;
			if (function_exists('isValidEmails'))	 if (!isValidEmails()) return false;
			if (function_exists('isValidUserURLs'))	 if (!isValidUserURLs()) return false;
			if (function_exists('isValidUserEmails'))	if (!isValidUserEmails()) return false;
		}
		if (task == 'plantask.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','task'); ?>');$('jform_title').focus();}
			else
			if (!$('jform_sid').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_PLAN'); ?>');$('jform_sid').focus();}
		}
	}
</script>
<script type="text/javascript">
	function change_plan(id)
	{
		if (id)
		{
		var url="<?php echo JURI::root()?>index.php?option=com_goals&task=plantask.getcustomfields&tmpl=component";
		var dan="id=" + id + "&tid=<?php echo (int)$this->item->id; ?>";
		var customfields =  document.id('customfields');
		 var myAjax = new Request.HTML({
				url:url,
				method: "post",
				data: dan,
				encoding:"utf-8",
				update: customfields

			});
		myAjax.send();
		} else document.id('customfields').set('html','');
	}

	window.addEvent('domready', function(){change_plan(document.id('jform_sid').value);});

</script>

<form action="<?php echo JRoute::_('index.php?option=com_goals&view=editrecord&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate"  enctype="multipart/form-data">
<div class="gl_dashboard form-horizontal">
	<h2 class="goals-form-name">
	 	<?php echo ($old)?JText::_('COM_GOALS_PLANTASK_EDIT_FORM_TITLE_EDIT'):JText::_('COM_GOALS_PLANTASK_EDIT_FORM_TITLE_NEW'); ?>
 	</h2>
    <div class="goals-form-set">
        <div class="control-group"><?php showJbField($this->form,'title'); ?></div>
		<div class="control-group"><?php showJbField($this->form,'date'); ?></div>
		<div class="control-group controls-row"><?php showJbField($this->form,'value'); ?><span class="controls metric"><?php if (isset($this->item->metric)) echo $this->item->metric ?></span></div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('result_mode');?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('result_mode');?>
			</div>
		</div>
		<div class="control-group"><?php showJbField($this->form,'sid'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'status'); ?></div>
		<div class="control-group wide-input"><?php showJbField($this->form,'description'); ?></div>
		<div class="clr"></div>
        <div id="customfields" class="control-group"></div>
        <div class="clr"></div>
    </div>
    <div class="goals-form-actions control-group">
        <div class="controls">
            <input type="button" class="btn" onclick="Joomla.submitbutton('plantask.save')" value="<?php echo JText::_('JSAVE') ?>" />
            <input type="button" class="btn" onclick="Joomla.submitbutton('plantask.cancel')" value="<?php echo JText::_('JCANCEL') ?>" />
        </div>
    </div>
	<input type="hidden" name="task" value="plantask.edit" />
	<input type="hidden" name="id" value="<?php echo ($old)?$this->item->id:0; ?>" />
	<input type="hidden" name="sid" value="<?php echo (isset($this->item->sid))?$this->item->sid:0; ?>" />
	<input type="hidden" name="pid" value="<?php echo (int)JRequest::getVar('pid'); ?>" />
	<?php $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') echo'<input type="hidden" name="tmpl" value="component" />';?>
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>
</div><!-- end #goals-wrap -->