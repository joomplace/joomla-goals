<?php

/**

* Goals component for Joomla 3.0

* @package Goals

* @author JoomPlace Team

* @Copyright Copyright (C) JoomPlace, www.joomplace.com

* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html

**/

defined('_JEXEC') or die('Restricted access');



?>

<div id="goals-wrap">

 <?php GoalsHelper::showDashHeader('','','','active'); ?>



    <div class="goals-content">

        <ul class="goals-list">

            <?php foreach ($this->goals as $goal) {

            if (!is_file(JPATH_SITE.'/'.$goal->image)) $goal->image='components/com_goals/assets/images/no_image.png';

            ?>

            <li class="goals-achievement">

                <div class="goals-item-image">

                    <a href="<?php echo JRoute::_('index.php?option=com_goals&view=goal&id='.$goal->id); ?>">

                    <?php

                        echo '<img src="'.JURI::root().$goal->image.'" />';

                    ?>

                    </a>

                </div>

                <div class="goals-finish"><h3><a href="<?php echo JRoute::_('index.php?option=com_goals&view=goal&id='.$goal->id); ?>"><?php echo $goal->title; ?></a></h3></div>

                <div class="goals-state">

                    <small><a class="state-link muted" href="<?php echo  JRoute::_('index.php?option=com_goals&view=records&gid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_TASKS')?> (<?php echo $goal->records_count ?>)</a></small>

                    <small><a class="state-link muted" href="<?php echo  JRoute::_('index.php?option=com_goals&view=milistones&gid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ACHIEVEMENTS_MILESTONES')?> (<?php echo $goal->milistones_count; ?>)</a></small>

                </div>

            </li>

             <?php } ?>



            <?php foreach ($this->plans as $goal) {



            if (!is_file(JPATH_SITE.'/'.$goal->image)) $goal->image='components/com_goals/assets/images/no_image.png';

            ?>

            <li class="goals-achievement">

                <div class="goals-item-image">

                    <a href="<?php echo JRoute::_('index.php?option=com_goals&view=plan&id='.$goal->id); ?>">

                        <?php

                        echo '<img src="'.JURI::root().$goal->image.'" />';

                        ?>

                    </a>

                </div>

                <div class="goals-finish"><h3><a href="<?php echo JRoute::_('index.php?option=com_goals&view=plan&id='.$goal->id); ?>"><?php echo $goal->title; ?></a></h3></div>

                <div class="goals-state">

                    <small><a class="state-link muted" href="<?php echo  JRoute::_('index.php?option=com_goals&view=plantasks&pid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ACHIEVEMENTS_PLANTASKS') ?> (<?php echo $goal->task_count ?>)</a></small>

                    <small><a class="state-link muted" href="<?php echo  JRoute::_('index.php?option=com_goals&view=stages&pid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ACHIEVEMENTS_STAGES') ?> (<?php echo $goal->stages_count; ?>)</a></small>

                </div>

            </li>

            <?php } ?>

        </ul>



    </div>




 <div class="clr"></div>
</div><!-- end #goals-wrap -->