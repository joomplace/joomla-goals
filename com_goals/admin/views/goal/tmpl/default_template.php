<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JFactory::getDocument()->addStyleSheet(JURI::root() . 'administrator/components/com_goals/assets/css/goals.css');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'goal.cancel') {
            Joomla.submitform(task, document.getElementById('item-form'));
        }
    }
</script>
<?php echo $this->loadTemplate('menu');?>
<table class="admin">
    <tbody>
    <tr>
        <td valign="top" class="lefmenutd">

        </td>
        <td valign="top" width="100%">
            <fieldset class="adminform" >
                <form action="index.php?option=com_goals&view=goal&layout=edit" method="post" id="item-form">
                    <label><?php echo JText::_('COM_GOALS_SELECT_GOAL_TEMPLATE') ?></label>
                    <select name="template_name">
                        <?php foreach ($this->templates as $template) {
                        echo '<option value="' . $template->id . '">' . $template->title . '</option>';
                    }
                        ?>
                        <input type="submit" value="<?php echo JText::_('COM_GOALS_SELECT_GOAL_TEMPLATE_CREATE_BUTTON') ?>">
                    </select>
                    <input type="hidden" name="task" value="" />
                    <?php echo JHtml::_('form.token'); ?>
                </form>
            </fieldset>
        </td>
    </tr>
    </tbody>
</table>
