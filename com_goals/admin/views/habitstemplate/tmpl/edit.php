<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'habitstemplate.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','habit template'); ?>');$('jform_title').focus();}
		}
	}
</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_goals&view=habitstemplate&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate form-horizontal" >
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
                        $days = array('1', '2', '3', '4', '5', '6', '7');
                        if (isset($this->item->days)) {

                            $days = explode(',', $this->item->days);

                        } else {
                            if ($this->form->getValue('days')) {
                                $days = explode(',', $this->form->getValue('days'));
                            }
                        }
                        }?>

                    <div class="control-group">
                        <div class="control-label"><?php echo JHTML::tooltip(JText::_('COM_GOALS_HABIT_DAYS_DESC'), JText::_('COM_GOALS_HABIT_DAYS'), '', JText::_('COM_GOALS_HABIT_DAYS'));?></div>
                        <div class="controls">
                        <table>
                        <tr>
                            <?php
                            for ($i = 1, $n = 7; $i < $n; $i++) {
                                ?>
                                <td><label style="min-width:10px !important;"
                                           for="<?php echo GoalsHelper::dayToStr($i, true);?>"><?php echo GoalsHelper::dayToStr($i, true);?></label>
                                </td>
                                <?php
                            }
                            ?>
                            <td><label style="min-width:10px !important;"
                                       for="<?php echo GoalsHelper::dayToStr(0, true);?>"><?php echo GoalsHelper::dayToStr(0, true);?></label>
                            </td>
                        </tr>
                        <tr>
                            <?php
                            for ($i = 1, $n = 7; $i < $n; $i++) {
                                ?>
                                <td><input id="<?php echo GoalsHelper::dayToStr($i, true);?>" type="checkbox"
                                           class="checkbox" name="<?php echo GoalsHelper::dayToStr($i);?>"
                                           value="<?php echo $i;?>" <?php if (in_array($i, $days)) echo ' checked="checked"'?> />
                                </td>
                                <?php
                            }
                            ?>
                            <td><input id="<?php echo GoalsHelper::dayToStr(0, true);?>" type="checkbox"
                                       class="checkbox" name="<?php echo GoalsHelper::dayToStr(0);?>"
                                       value="7" <?php if (in_array('7', $days)) echo ' checked="checked"'?>/></td>
                        </tr>
                        </table>   
                    </div>    
                </div>  

        </div>
<div>                       
<input type="hidden" name="task" value="item.edit" />
<?php echo JHtml::_('form.token'); ?>
</div>