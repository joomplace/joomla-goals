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
	echo '<td class="gl_edittitle">';
	echo $form->getLabel($name);
	echo '</td><td>';
	echo $form->getInput($name);
	echo '</td>';
}



JHTML::_('behavior.modal', 'a.modal');
$old = false;
if (isset($this->item->id)) $old=true;
?>

<div id="goals-wrap">
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'habitlog.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php //echo $this->form->getField('description')->save();
			?>
			Joomla.submitform(task);
		}
			else {
				if (!$('jform_hid').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_HID'); ?>');$('jform_hid').focus();}
				else
				if (!$('jform_date').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_DATE'); ?>');$('jform_date').focus();}
			}
	}
</script>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="gl_dashboard">
	<div class="gl_my_dashboars">
	 	<span class="gl_dashboard_text">
			<?php echo JText::_('COM_GOALS_HABLOG_EDIT_FORM_TITLE_NEW'); ?>
		</span>
	 		<div class="gl_dashboard_buttons_edit">
		 		<input type="button" onclick="Joomla.submitbutton('habitlog.save')" value="<?php echo JText::_('JSAVE') ?>" />
				<input type="button" onclick="Joomla.submitbutton('habitlog.cancel')" value="<?php echo JText::_('JCANCEL') ?>" />
	 		</div>
 	</div>
 <div class="gl_goals">
		<table cellspacing="1" class="goalfieldslist" border="0" width="100%">
			<tr class="row0"><?php showJbField($this->form,'hid'); ?></tr>
			<tr class="row1"><?php showJbField($this->form,'status'); ?></tr>
			<tr class="row0"><?php showJbField($this->form,'date'); ?></tr>
		</table>
</div>
</div>
	<input type="hidden" name="task" value="habitlog.edit" />
	<input type="hidden" name="id" value="<?php echo ($old)?$this->item->id:0; ?>" />
	<?php $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') echo'<input type="hidden" name="tmpl" value="component" />';?>
	<?php echo JHtml::_('form.token'); ?>
</form>
</div><!-- end #goals-wrap -->
