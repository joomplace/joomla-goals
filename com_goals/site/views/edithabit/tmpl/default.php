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
	Joomla.submitbutton = function(task) {
		if (task == 'habit.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php //echo $this->form->getField('description')->save();
			?>
			Joomla.submitform(task);
		}
		else {
			if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','habit'); ?>');$('jform_title').focus();}
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_goals&view=edithabit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
<div class="gl_dashboard">
	<h2><?php echo ($old)?JText::_('COM_GOALS_HAB_EDIT_FORM_TITLE_EDIT'):JText::_('COM_GOALS_HAB_EDIT_FORM_TITLE_NEW'); ?></h2>
    <div class="gl_goals">
        <div class="control-group"><?php showJbField($this->form,'title'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'weight'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'finish'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'type'); ?></div>

        <?php
        $days = array();
        if (isset($this->item->days)) {

            $days = explode(',', $this->item->days);


        } else {
            if ($this->form->getValue('days')) {
                $days = explode(',', $this->form->getValue('days'));
            }
        }
                ?>
                <div class="control-group">
                    <label class="control-label"><?php echo JText::_('COM_GOALS_HABIT_DAYS');?>:</label>

                    <div class="controls">
                    	<table class="goals_manager_calend_table">
                    	    <tr>
                    	        <?php
                    	        for ( $i = 1, $n = 7; $i < $n; $i++ )
                    	        {
                    	            ?>
                    	            <td><label style="min-width:10px !important;" for="<?php echo GoalsHelperFE::dayToStr($i,true);?>"><?php echo GoalsHelperFE::dayToStr($i,true);?></label></td>
                    	            <?php
                    	        }
                    	        ?>
                    	        <td><label style="min-width:10px !important;" for="<?php echo GoalsHelperFE::dayToStr(0,true);?>"><?php echo GoalsHelperFE::dayToStr(0,true);?></label></td>
                    	    </tr>
                    	    <tr>
                    	    <?php
                    	        for ( $i = 1, $n = 7; $i < $n; $i++ )
                    	        {
                    	            ?>
                    	                <td><input id="<?php echo GoalsHelperFE::dayToStr($i,true);?>" type="checkbox" class="checkbox" name="<?php echo GoalsHelperFE::dayToStr($i);?>" value="<?php echo $i;?>"  value="<?php echo $i;?>" <?php if (in_array($i, $days)) echo ' checked="checked"'?> />
                    	            <?php
                    	        }
                    	        ?>
                    	        <td><input id="<?php echo GoalsHelperFE::dayToStr(0,true);?>" type="checkbox" class="checkbox" name="<?php echo GoalsHelperFE::dayToStr(0);?>"  value="7" <?php if (in_array('7', $days)) echo ' checked="checked"'?>/></td>
                    	    </tr>
                    	</table>
                    </div>
                </div>
        <div class="control-group">
		 		<div class="controls">
		 			<input type="button" class="btn" onclick="Joomla.submitbutton('habit.save')" value="<?php echo JText::_('JSAVE') ?>" />
                    <input type="button" class="btn" onclick="Joomla.submitbutton('habit.cancel')" value="<?php echo JText::_('JCANCEL') ?>" />
		 		</div>
        </div>
    </div>
    <?php showJbField($this->form,'featured'); ?>
	<input type="hidden" name="task" value="habit.edit" />
	<input type="hidden" name="id" value="<?php echo ($old)?$this->item->id:0; ?>" />
	<?php $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') echo'<input type="hidden" name="tmpl" value="component" />';?>
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>
</div><!-- end #goals-wrap -->