<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JFactory::getDocument()->addStyleSheet(JURI::root() . 'administrator/components/com_goals/assets/css/goals.css');
?>
<form action="index.php?option=com_goals&view=goal&layout=edit" method="post">
    <label><?php echo JText::_('COM_GOALS_SELECT_GOAL_TEMPLATE') ?></label>
    <select name="template_name">
        <?php foreach ($this->templates as $template) {
        echo '<option value="' . $template->id . '">' . $template->title . '</option>';
    }
        ?>
        <input type="submit" value="<?php echo JText::_('COM_GOALS_SELECT_GOAL_TEMPLATE_CREATE_BUTTON') ?>">
    </select>
</form>
<!--Closing  div id="goals" -->
</div>
