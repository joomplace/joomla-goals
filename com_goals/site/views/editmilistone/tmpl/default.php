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

require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'goals.php');

JHTML::_('behavior.modal', 'a.modal');
$old = false;
if (isset($this->item->id)) $old=true;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'milistone.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {

			if(document.getElementById('jform_gid').options[document.getElementById('jform_gid').selectedIndex].value === "" && task != 'milistone.cancel'){
				alert("Select Goal, please!");
				return false;
			}

			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','milestone'); ?>');$('jform_title').focus();}
			else
			if (!$('jform_gid').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_GOAL'); ?>');$('jform_gid').focus();}
		}
	}
</script>
<div id="goals-wrap">
    <div class="gl_dashboard">
        <?php GoalsHelperGoals::showDashHeader(' ','active',' ',' '); ?>
    </div>
<form action="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'editmilistone','id'=>$this->item->id))); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="gl_dashboard  form-horizontal">
	<h2 class="goals-form-name text-center">
	 	<?php echo ($old)?JText::_('COM_GOALS_MIL_EDIT_FORM_TITLE_EDIT'):JText::_('COM_GOALS_MIL_EDIT_FORM_TITLE_NEW'); ?>
 	</h2>

    <div class="goals-form-set">
		<div class="control-group"><?php showJbField($this->form,'title'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'gid'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'duedate'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'value'); ?></div>
		<div class="control-group wide-input"><?php showJbField($this->form,'description'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'status'); ?></div>
    </div>
    <div class="goals-form-actions control-group">
        <div class="controls">
        	<input type="button" class="btn" onclick="Joomla.submitbutton('milistone.save')" value="<?php echo JText::_('JSAVE') ?>" />
        	<input type="button" class="btn" onclick="Joomla.submitbutton('milistone.cancel')" value="<?php echo JText::_('JCANCEL') ?>" />
        </div>
    </div>
	<input type="hidden" name="task" value="milistone.edit" />
	<input type="hidden" name="id" value="<?php echo ($old)?$this->item->id:0; ?>" />
	<input type="hidden" name="gid" value="<?php echo (isset($this->item->gid))?$this->item->gid:0; ?>" />
    <input type="hidden" name="return" value="<?php echo GoalsHelperGoals::getReturnURL(null,array('view'=>'milistones','gid'=>JFactory::getApplication()->input->get('gid'))); ?>" />

	<?php $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') echo'<input type="hidden" name="tmpl" value="component" />';?>
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>
</div><!-- end #goals-wrap -->