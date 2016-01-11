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
    function change_plan(id)
    {
        var url="<?php echo JURI::root()?>administrator/index.php?option=com_goals&task=plantasktemplate.getcustomfields&tmpl=component";
        var dan="id=" + id + "&tid=<?php echo (int)$this->item->id; ?>";
        var customfields =  $('customfields');
        var myAjax = new Request.HTML({
            url:url,
            method: "post",
            data: dan,
            encoding:"utf-8",
            update: customfields

        });
        myAjax.send();
    }

    function function_exists( function_name ) {
        if (typeof function_name == 'string'){
            return (typeof window[function_name] == 'function');
        } else{
            return (function_name instanceof Function);
        }
    }


    window.addEvent('domready', function(){
        change_plan($('jform_sid').value);
    });

    Joomla.submitbutton = function(task) {
        if (task != 'plantasktemplate.cancel')
        {
            if (function_exists('isValidURLs'))	if (!isValidURLs()) return false;
            if (function_exists('isValidEmails'))	 if (!isValidEmails()) return false;
            if (function_exists('isValidUserURLs'))	 if (!isValidUserURLs()) return false;
            if (function_exists('isValidUserEmails'))	if (!isValidUserEmails()) return false;
        }

        if (task == 'plantasktemplate.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
            Joomla.submitform(task, document.getElementById('item-form'));
        }
        else {
            if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','task'); ?>');$('jform_title').focus();}
            else
            if (!$('jform_gid').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_GOAL'); ?>');$('jform_gid').focus();}
        }
    }
</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_goals&view=plantasktemplate&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate form-horizontal" >
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