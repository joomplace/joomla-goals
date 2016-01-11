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
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'goals.php' );

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
		if (task != 'record.cancel')
		{
			if (function_exists('isValidURLs'))	if (!isValidURLs()) return false;
			if (function_exists('isValidEmails'))	 if (!isValidEmails()) return false;
			if (function_exists('isValidUserURLs'))	 if (!isValidUserURLs()) return false;
			if (function_exists('isValidUserEmails'))	if (!isValidUserEmails()) return false;
		}
		if (task == 'record.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','record'); ?>');$('jform_title').focus();}
			else
			if (!$('jform_gid').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_GOAL'); ?>');$('jform_gid').focus();}
		}
	}
</script>
<script type="text/javascript">
	function change_goal(id)
	{
		if (id)
		{
		var url="<?php echo JURI::root()?>index.php?option=com_goals&task=record.getcustomfields&tmpl=component<?php //if($this->item->negative) echo '&negative=true'; ?>";
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

</script>
    <div class="gl_dashboard">
        <?php GoalsHelper::showDashHeader('','active','',''); ?>
    </div>
<form action="<?php echo JRoute::_('index.php?option=com_goals&view=editrecord&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate"  enctype="multipart/form-data">
<div class="gl_dashboard form-horizontal">
	<h2 class="goals-form-name text-center">
	 	<?php echo ($old)?JText::_('COM_GOALS_REC_EDIT_FORM_TITLE_EDIT'):JText::_('COM_GOALS_REC_EDIT_FORM_TITLE_NEW'); ?>
 	</h2>
    <div class="goals-form-set">
        <div class="control-group"><?php showJbField($this->form,'title'); ?></div>
		<div class="control-group wide-input"><?php showJbField($this->form,'description'); ?></div>
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
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('gid');?>
			</div>
			<div class="goals-form-datainput controls">
				<?php echo $this->goals; ?>
			</div>
		</div>
        <div class="clr"></div>
        <div id="customfields" class="control-group"></div>
        <div class="clr"></div>
    </div>
    <div class="goals-form-actions control-group">
        <div class="controls">
            <input type="button" class="btn" onclick="Joomla.submitbutton('record.save')" value="<?php echo JText::_('JSAVE') ?>" />
            <input type="button" class="btn" onclick="Joomla.submitbutton('record.cancel')" value="<?php echo JText::_('JCANCEL') ?>" />
        </div>
    </div>
	<input type="hidden" name="task" value="record.edit" />
	<input type="hidden" name="id" value="<?php echo ($old)?$this->item->id:0; ?>" />
	<input type="hidden" name="gid" value="<?php echo (isset($this->item->gid))?$this->item->gid:0; ?>" />
	<?php $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') echo'<input type="hidden" name="tmpl" value="component" />';?>
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>
</div><!-- end #goals-wrap -->
<script type="text/javascript">
	change_goal(document.getElementById('jformgid').options[document.getElementById('jformgid').selectedIndex].value);
</script>
