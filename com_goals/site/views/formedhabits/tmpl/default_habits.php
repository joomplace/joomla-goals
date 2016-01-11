<?php

/**

* Goals component for Joomla 3.0

* @package Goals

* @author JoomPlace Team

* @Copyright Copyright (C) JoomPlace, www.joomplace.com

* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html

**/

defined('_JEXEC') or die('Restricted access');

$items = $this->habits;



$tmpl = JRequest::getVar('tmpl');

if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';



if (sizeof($items)) {

    ?>



<div class="goals-content">

    <ul class="goals-list goals-habits-list ui-sortable">

        <?php

        $counter = 0;



        for ($i = 0, $n = sizeof($items); $i < $n; $i++) {



            $habit = $items[$i];

            if (!$habit->complete) continue;

            $counter++;

            ?>

            <li class="goals-list-layout" id="habit_<?php echo $habit->id;?>">

                <div class="goals-item-frame">

                    <div class="goals-item goals-item-habbit clearfix">

                        <div class="gl_goal_status_left_part">



                            <div class="gl_habit_progress">

                                <img src="components/com_goals/assets/images/check_checked.png" alt="Formed"/>

                            </div>

                        </div>

                        <div class="gl_goal_left_part">

                            <div class="gl-goal-title">

                                <?php /* 

                                no-more-bad-habbit use only 

                                */ ?>

                                <h3>

                                    <a href="<?php echo JRoute::_('index.php?option=com_goals&view=habithistory&id=' . (int)$habit->id . $tmpl);?>"><?php echo $habit->title;?></a>

                                </h3>

                            </div>

                        </div>

                        <div class="goal-habit-actions">

                            <?php if ($habit->type=='+') {?>

                            <a class="goals-diagr"

                               href="<?php echo JRoute::_('index.php?option=com_goals&view=habithistory&id=' . (int)$habit->id . $tmpl);?>"><img

                                    src="components/com_goals/assets/images/diagr-small.png" alt="Statistics" />  <span class="statistic-totals"><?php echo JText::sprintf('COM_GOALS_HABITS_DONE_CHECKS', $habit->finish)?></span></a>

                           





                            <?php } else { ?>

                            <a class="goals-diagr"

                               href="<?php echo JRoute::_('index.php?option=com_goals&view=habithistory&id=' . (int)$habit->id . $tmpl);?>"><img

                                    src="components/com_goals/assets/images/diagr-small.png" alt="Statistics">  <span class="statistic-totals"><?php echo JText::sprintf('COM_GOALS_HABITS_DONE_NEGATIVE_CHECKS', $habit->finish)?></span></a>

                           

                        <?php } ?>

                        </div>

                    </div>

                </div>

            </li>



            <?php } ?>

    </ul>

</div>

<?php

} else {

    ?>

<div class="gl_msntf"><?php echo JText::_('COM_GOALS_HABITS_NOT_FOUND');?></div>

<?php }

