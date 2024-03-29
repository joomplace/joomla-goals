<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'goalstemplate.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','goalstemplate'); ?>');$('jform_title').focus();}
			else 
			if (!$('jform_cid').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_CATEGORY'); ?>');$('jform_cid').focus();}

			else 
			if (!$('jform_start').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_ENTER_STARTVAL'); ?>');$('jform_start').focus();}
			else 
			if (!$('jform_finish').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_ENTER_FINVAL'); ?>');$('jform_finish').focus();}
		}
	}
</script>
<?php echo $this->loadTemplate('menu');?>
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_goals&view=goalstemplate&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate form-horizontal" >
	<div class="row-fluid">
	<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<?php $i=1;
				foreach ($this->form->getFieldsets() as $fieldset) {
				 echo ($i==1)?'<li class="active"><a href="#'.$fieldset->name.'" data-toggle="tab">'.JText::_($fieldset->label).'</a></li>':'<li><a href="#'.$fieldset->name.'" data-toggle="tab">'.JText::_($fieldset->label).'</a></li>'; 
				 $i++;
				} ?>
			</ul>				
					<div class="tab-content">
					<?php $j=1;
					foreach ($this->form->getFieldsets() as $fieldset) {
						$fields = $this->form->getFieldset($fieldset->name);
						
						// Begin Tabs 
						echo ($j==1)?'<div class="tab-pane active" id="'.$fieldset->name.'">':'<div class="tab-pane" id="'.$fieldset->name.'">';
								foreach($this->form->getFieldset($fieldset->name) as $field) {
									echo ($field->hidden == 1)? $field->input: '<div class="control-group"><div class="control-label">'.$field->label.'</div><div class="controls">'.$field->input.'</div></div>'; 
								}
						$j++;	

						// End tab details 
					    echo '</div>';
						}?>
				</div>	
		</div>
<div>						
<input type="hidden" name="task" value="item.edit" />
<?php echo JHtml::_('form.token'); ?>
</div>