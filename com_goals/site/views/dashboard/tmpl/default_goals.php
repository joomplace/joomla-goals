<?php

/**

* Goals component for Joomla 3.0

* @package Goals

* @author JoomPlace Team

* @Copyright Copyright (C) JoomPlace, www.joomplace.com

* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html

**/



defined('_JEXEC') or die('Restricted access');


include_once(JPATH_SITE.'/components/com_goals/helpers/route.php');

$manage_allowed = $this->manage_allowed;

$items = $this->goals;

$tmpl = JRequest::getVar('tmpl');

if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';

?>

<div class="goals-content">

    <?php if (sizeof($items)) { ?>

    <ul class="goals-list">

        <?php

          $counter = 0;

        for ($i = 0, $n = sizeof($items); $i < $n; $i++) {

            $goal = $items[$i];


            $status = $goal->status;

            $procent = (($goal->percent)>100)?100:$goal->percent;

            if ($goal->percent==100) continue;

            $counter++;





            ?>

            <li class="goals-list-layout" id="<?php echo (($goal->type == 'goal')?'goal-':'plan-').$goal->id; ?>">

                <div class="goals-drag">

                    <div class="goals-drag-pict"></div>

                    <div class="goals-item clearfix" style="border-left: 4px solid <?php echo $goal->status; ?>">

                        <div class="goals-left">

                            <h3 class="gl-goal-title">

                                <?php

                                $glink = JRoute::_('index.php?option=com_goals&view=goal&id=' . $goal->id . $tmpl);

                                $glink = $goal->type == 'goal'?JRoute::_('index.php?option=com_goals&view=goal&id=' . $goal->id . $tmpl):JRoute::_('index.php?option=com_goals&view=plan&id=' . $goal->id . $tmpl);
                                $glink=GoalsHelperRoute::buildLink(array('view'=>$goal->type,'id'=>$goal->id));
                                echo '<span class="goals-tag">[' . JText::_('COM_GOALS_' . (($goal->type == 'goal') ? 'GOAL' : 'PLAN') . '_FEATURED_LABEL') . ']&nbsp;</span><a href="' . $glink . '">' . $goal->title . '</a>';

                                ?>

                            </h3>

                            <div class="gl-goal-short-details">

                                <div class="goals-state">

                                <?php

                                if ($goal->type == 'goal') {





                                    if (isset($goal->records_count)) {

                                        $rlink = JRoute::_('index.php?option=com_goals&view=records&gid=' . (int)$goal->id . $tmpl);

                                        echo '<a href="' . $rlink . '" title="" class="gl_task_count">' . JText::_('COM_GOALS_TASKS') . ' (' . (int)$goal->records_count . ')</a>';

                                    }

                                    if (isset($goal->milistones_count)) {

                                        $rlink = JRoute::_('index.php?option=com_goals&view=milistones&gid=' . (int)$goal->id . $tmpl);

                                        echo '<a href="' . $rlink . '" title="" class="gl_task_count">' . JText::_('COM_GOALS_MILISTONES') . ' (' . (int)$goal->milistones_count . ')</a>';

                                        echo '</span>';

                                    }





                                } else {

                                    if (isset($goal->plantasks_count)) {

                                        $rlink = JRoute::_('index.php?option=com_goals&view=plantasks&pid=' . (int)$goal->id . $tmpl);

                                        echo '<a href="' . $rlink . '" title="" class="gl_task_count">' . JText::_('COM_GOALS_PLANTASKS') . ' (' . (int)$goal->plantasks_count . ')</a>';

                                    }


                                    ?>



                                    <?php

                                }

                                ?>

                                </div>


                                <div class="goals-date">
                                    <?php

                                    if (isset($goal->startup)){
                                        if ($goal->startup != '0000-00-00 00:00:00') {
                                            echo '<span class="date-by">' . JText::_('COM_GOALS_STARTON') . ': ' . JHtml::_('date', $goal->startup, JText::_('DATE_FORMAT_LC3')) . '</span>';

                                            if (isset($goal->tillleft)) {
                                                echo '<span class="date-rest">' . $goal->tillleft . '</span>';

                                            }
                                        }
                                        else{
                                            echo '<span class="date-by"> </span>';
                                        }
                                    }
                                    else{
                                        echo '<span class="date-by"> </span>';
                                    }
                                    ?>
                                </div>
                                <div class="goals-date">
                                    <?php

                                    if (isset($goal->deadline)){
                                        if ($goal->deadline != '0000-00-00 00:00:00') {
                                            echo '<span class="date-by">' . JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $goal->deadline, JText::_('DATE_FORMAT_LC3')) . '</span>';

                                            if (isset($goal->left)) {
                                                echo '<span class="date-rest">' . $goal->left . '</span>';

                                            }
                                        }
                                    }
                                    ?>
                                </div>

                            </div>

                            <div style="clear:both"></div>

                        </div>

                        <div class="goals-right">

                            <div class="goal-item-progress">

                            	<div class="progress progress-small progress-danger progress-striped">

                            	    <div class="bar" style="width:<?php echo $procent?>%; background-color: <?php echo $goal->status?>"></div>

                            	</div>

                            	<div class="progressbar_label_right"><?php echo $procent?>%</div>

                            </div>

                        </div>

                        <?php if($manage_allowed){ ?>
                        <div class="goal-item-actions goals-right-hover">

                            <div class="goals-state">

                                <?php if ($goal->type == 'goal') { ?>

                                <a class="state-link"

                                   href="<?php echo JRoute::_('index.php?option=com_goals&view=editrecord&gid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ADD_RECORD')?></a>

                                <a class="state-link"

                                   href="<?php echo JRoute::_('index.php?option=com_goals&view=editmilistone&gid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ADD_MILESTONE')?></a>

                                <?php } else { ?>

                                <a class="state-link"

                                   href="<?php echo JRoute::_('index.php?option=com_goals&view=editplantask&pid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ADD_TASK')?></a>

                                <a class="state-link"

                                   href="<?php echo JRoute::_('index.php?option=com_goals&view=editstage&pid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ADD_STAGE')?></a>

                                <?php } ?>

                            </div>

                            <div class="goals-edit-item">

                                <a class="goals-diagr" title="" href="<?php echo JRoute::_('index.php?option=com_goals&view='.$goal->type.'&id='.$goal->id); ?>"><img src="components/com_goals/assets/images/diagr.png" alt=""/></a>

                                <?php if ($goal->type == 'goal') { ?>

                                <div class="btn-group">

                                    <button class="btn" onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editgoal&id='.$goal->id); ?>'; return false;"><?php echo JText::_('COM_GOAL_GOAL_EDITBUTTON'); ?></button>

                                    <button class="btn dropdown-toggle" data-toggle="dropdown">

                                        <span class="caret"></span>

                                    </button>

                                    <ul class="dropdown-menu">

                                        <li><a href="<?php echo JRoute::_('index.php?option=com_goals&task=goal.featuredOnOff&id='.$goal->id); ?>"><?php echo JText::_('COM_GOAL_GOAL_REMOVE_FROM_FEATURED'); ?></a>

                                        </li>

                                        <li><a href="javascript:void(0)" onclick="removeGoal('<?php echo JRoute::_('index.php?option=com_goals&task=goal.delete&id='.$goal->id); ?>')

                                                "><?php echo JText::_('COM_GOAL_GOAL_REMOVE_DELETE'); ?></a></li>

                                    </ul>

                                </div>

                            </div>

                            <?php } else { ?>



                            <div class="btn-group">

                                <button class="btn" onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editplan&id='.$goal->id); ?>'; return false;"><?php echo JText::_('COM_GOAL_PLAN_EDITBUTTON'); ?></button>

                                <button class="btn dropdown-toggle" data-toggle="dropdown">

                                    <span class="caret"></span>

                                </button>

                                <ul class="dropdown-menu">

                                    <li><a href="<?php echo JRoute::_('index.php?option=com_goals&task=plan.featuredOnOff&id='.$goal->id); ?>"><?php echo JText::_('COM_GOAL_GOAL_REMOVE_FROM_FEATURED'); ?></a>

                                    </li>

                                    <li><a href="javascript:void(0)" onclick="removePlan('<?php echo JRoute::_('index.php?option=com_goals&task=plan.delete&id='.$goal->id); ?>')

                                            "><?php echo JText::_('COM_GOAL_PLAN_REMOVE_DELETE'); ?></a></li>

                                </ul>

                            </div>

                            <?php } ?>

                        </div>
                        <?php } ?>

                        <div class="clr"></div>

                    </div>

                    <div class="gl_goal_details">

                        <div class="clr"></div>

                    </div>

                </div>

            </li>



            <?php }?>

    </ul>

    <?php } else { ?>

    <div class="gl_msntf"><?php echo JText::_('COM_FEATURED_NOT_FOUND');?></div>

    <?php } ?>

    <!-- <?php if (!isset($counter)) { ?> -->

    <!-- <div class="gl_msntf"><?php echo JText::_('COM_FEATURED_NOT_FOUND');?></div> -->

    <!-- <?php } ?> -->

    <div class="clr"></div>

</div>

    <script type="text/javascript">

        function removeGoal(url) {



            if (confirm("<?php echo JText::_('COM_GOALS_REMOVE_GOAL_MESSAGE') ?>")) {

                window.location.href=url;

            }

        }

        function removePlan(url) {



            if (confirm("<?php echo JText::_('COM_GOALS_REMOVE_PLAN_MESSAGE') ?>")) {

                window.location.href=url;

            }

        }



    </script>