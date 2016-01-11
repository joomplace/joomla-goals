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

$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';

function showJbField($form, $name='')
{
	echo '<div class="control-label">';
	echo $form->getLabel($name);
	echo '</div><div class="goals-form-datainput controls">';
	echo $form->getInput($name);
	echo '</div>';
}


JHTML::_('behavior.modal', 'a.popup');
$old = false;
if (isset($this->item->id)) $old=true;
?>
<div id="goals-wrap">
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'plan.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','plan'); ?>');$('jform_title').focus();}
			else
			if (!$('jform_cid').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_CATEGORY'); ?>');$('jform_cid').focus();}
            else
            if (!$('jform_startup').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_ENTER_STARTUP'); ?>');$('jform_startup').focus();}
			else
			if (!$('jform_deadline').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_ENTER_DEADLINE'); ?>');$('jform_deadline').focus();}
			else
			if (!$('jform_start').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_ENTER_STARTVAL'); ?>');$('jform_start').focus();}
			else
			if (!$('jform_finish').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_ENTER_FINVAL'); ?>');$('jform_finish').focus();}
		}
	}
</script>
<script type="text/javascript">
	<?php if ($old==true)
	{ ?>
	function updateuserfields()
	{
		var url="<?php echo JURI::root()?>/index.php?option=com_goals&task=plan.getuserfields&tmpl=component";
		var dan="id=<?php echo (int)$this->item->id; ?>";
		var gluserfields =  $('gluserfields');
		 var myAjax = new Request.HTML({
				url:url,
				method: "post",
				data: dan,
				encoding:"utf-8",
				update: gluserfields

			});
		myAjax.send();
	}
	<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_goals&view=editplan&id='.(int) $this->item->id.$tmpl); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="gl_dashboard form-horizontal">
	<h2 class="goals-form-name">
	 	<?php echo ($old)?JText::_('COM_GOALS_EDIT_FORM_PLAN_TITLE_EDIT'):JText::_('COM_GOALS_EDIT_FORM_PLAN_TITLE_NEW'); ?>
 	</h2>
    <div class="goals-form-set">
		<div class="control-group"><?php showJbField($this->form,'title'); ?></div>
		<div class="control-group"><?php showJbField($this->form,'cid'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'startup'); ?></div>
		<div class="control-group"><?php showJbField($this->form,'deadline'); ?></div>
		<div class="control-group"><?php showJbField($this->form,'start'); ?></div>
		<div class="control-group"><?php showJbField($this->form,'finish'); ?></div>
		<div class="control-group"><?php showJbField($this->form,'metric'); ?></div>
		<div class="control-group"><?php showJbField($this->form,'image'); ?></div>
		<?php if (GoalsHelper::getSettings()->allow_userfields) { ?>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('userfields');?></div>
			<div class="controls"><?php echo $this->form->getInput('userfields');?></div>
		</div>
		<div class="control-group wide-input"><?php showJbField($this->form,'description'); ?></div>
		<div class="control-group">
			<?php if ($old) { ?><a class="popup btn" href="<?php echo  JRoute::_('index.php?option=com_goals&view=editfield&tmpl=component&gid='.(int) $this->item->id); ?>" rel="{handler: 'iframe', size: {x: 640, y: 480}}" ><?php echo JText::_('COM_GOALS_ADD_NEW_FIELD');?></a>
			<?php }else { echo '<div class="controls"><small class="help-block">'.JText::_('COM_GOALS_ADD_NEW_FIELD_NOTE').'</small></div>';}?>
		</div>
		<?php } ?>
    </div>
    <div class="goals-form-actions control-group">
        <div class="controls">
        	<input type="button" class="btn" onclick="Joomla.submitbutton('plan.save')" value="<?php echo JText::_('JSAVE') ?>" />
        	<input type="button" class="btn" onclick="Joomla.submitbutton('plan.cancel')" value="<?php echo JText::_('JCANCEL') ?>" />
        </div>
    </div>
    <?php showJbField($this->form,'template'); ?>
    <?php showJbField($this->form,'featured'); ?>
    <input type="hidden" name="jform_tempid" value="<?php echo $this->tempid; ?>" />
	<input type="hidden" name="task" value="plan.edit" />
	<input type="hidden" name="id" value="<?php echo ($old)?$this->item->id:0; ?>" />
	<?php $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') echo'<input type="hidden" name="tmpl" value="component" />';?>
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>
</div><!-- end #goals-wrap -->