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
?>
<div id="goals-wrap">
    <?php GoalsHelperFE::showHabitHeader('','active',''); ?>

    <div class="goals-content">
        <h2><?php echo JText::_('COM_GOALS_SELECT_TEMPLATE_FOR_HABIT'); ?>:</h2>
        <ul class="goals-list">
            <?php foreach($this->items as $template) {

           $template->image=($template->type=="+")?'components/com_goals/assets/images/good_habit.jpg':'components/com_goals/assets/images/bad_habit.jpg';
            $template->image ='components/com_goals/assets/images/no_image.png';
            ?>
            <li class="goals-achievement">
                <div class="goals-item-image">
                    <a href="<?php echo JRoute::_('index.php?option=com_goals&view=edithabit&tempid='.$template->id); ?>"><img width="200px" src="<?php echo JURI::root().$template->image ?>" /></a>
                </div>
                <div class="goals-finish"><h3><a href="<?php echo JRoute::_('index.php?option=com_goals&view=edithabit&tempid='.$template->id); ?>"><?php echo $template->title; ?></a></h3></div>
            </li>
            <?php } ?>
        </ul>
    </div>

</div><!-- end #goals-wrap -->