<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$items = $this->goals;
$date_format = str_replace('%', '', $this->settings->chart_date_format);
?>
<div class="goals-content">
<?php

$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
?>
			  <?php if (sizeof($items)) {    ?>
                    <ul class="goals-list">
    <?php
					  	for ( $i = 0, $n = sizeof( $items ); $i < $n; $i++ )
					  	{
					  		$plan = $items[$i];
                            $procent = (($plan->percent)>100)?100:$plan->percent;

				  ?>

								 <li class="goals-list-layout">
                                     <div class="goals-drag">
                                        <div class="goals-item clearfix" style="border-left: 5px solid <?php echo $plan->status; ?>">
                                            <div class="goals-left">
                                                <div class="goals-star star-active <?php echo (!$plan->featured)?'star-nonfeatured':'star-featured'; ?>">
                                                    <a href="<?php echo JRoute::_('index.php?option=com_goals&task=plan.featuredOnOff&id='.$plan->id); ?>" title="<?php echo (!$plan->featured)?'Add to featured':'Remove from featured'; ?>">&#x2605;</a>
                                                </div>
                                                <h3 class="gl-goal-title">
                                                    <?php
                                                        $glink = JRoute::_('index.php?option=com_goals&view=plan&id='.$plan->id.$tmpl);
                                                        echo '<a href="'.$glink.'">'.$plan->title.'</a>';
                                                    ?>
                                                </h3>
                                                <div class="gl-goal-short-details">
                                                     <div class="goals-state">
                                                    <?php


                                                    if (isset($plan->task_count)) {
                                                        $rlink = JRoute::_('index.php?option=com_goals&view=plantasks&pid=' . (int)$plan->id . $tmpl);
                                                        echo '<a href="' . $rlink . '" title=""  class="gl_task_count">' . JText::_('COM_GOALS_PLANTASKS') . ' (' . (int)$plan->task_count . ')</a> ';
                                                    }
    ?>                                              </div>


                                                    <div class="goals-date">
                                                        <?php

                                                        if (isset($plan->startup)){
                                                            if ($plan->startup != '0000-00-00 00:00:00') {
                                                                echo '<span class="date-by">' . JText::_('COM_GOALS_STARTON') . ': ' . JHtml::_('date', $plan->startup, JText::_('DATE_FORMAT_LC3')) . '</span>';

                                                                if (isset($plan->tillleft)) {
                                                                    echo '<span class="date-rest">' . $plan->tillleft . '</span>';

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

                                                        if (isset($plan->deadline)){
                                                            if ($plan->deadline != '0000-00-00 00:00:00') {
                                                                echo '<span class="date-by">' . JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $plan->deadline, JText::_('DATE_FORMAT_LC3')) . '</span>';

                                                                if (isset($plan->left)) {
                                                                    echo '<span class="date-rest">' . $plan->left . '</span>';

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
                                                	<div class="progress progress-small progress-striped">
                                                	    <div class="bar" style="width:<?php echo $procent?>%; background-color: <?php echo $plan->status?>"></div>
                                                	</div>
                                                	<div class="progressbar_label_right"><?php echo $procent;?>%</div>
                                                </div>

                                                <div class="clr"></div>
                                            </div>
                                            
                                            <div class="goal-item-actions goals-right-hover">
                                                <div class="goals-state">
                                                    <a class="state-link"
                                                        href="<?php echo JRoute::_('index.php?option=com_goals&view=editplantask&pid='.$plan->id); ?>"><?php echo JText::_('COM_GOALS_ADD_TASK')?></a>
                                                    <a class="state-link"
                                                        href="<?php echo JRoute::_('index.php?option=com_goals&view=editstage&pid='.$plan->id); ?>"><?php echo JText::_('COM_GOALS_ADD_STAGE')?></a>

                                                </div>
                                                <div class="goals-edit-item">
                                                	<a class="goals-diagr" title="" href="<?php echo $glink ?>"><img src="components/com_goals/assets/images/diagr.png"       alt=""/></a>
                                                	<div class="btn-group">
                                                	    <button class="btn" onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editplan&id='.$plan->id) ?>'"><?php echo JText::_('COM_GOAL_PLAN_EDITBUTTON'); ?></button>
                                                	    <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                	        <span class="caret"></span>
                                                	    </button>
                                                	    <ul class="dropdown-menu">
                                                	        <li><a onclick="removeGoalFromFeatured('<?php echo JRoute::_('index.php?option=com_goals&task=plan.featuredOnOff&id='.$plan->id); ?>')" href="javascript:void(0)"><?php echo ($plan->featured)?JText::_('COM_GOAL_GOAL_REMOVE_FROM_FEATURED'):JText::_('COM_GOAL_GOAL_ADD_TO_FEATURED'); ?></a>
                                                	        </li>
                                                            <li><a href="javascript:void(0)" onclick="removePlan('<?php echo JRoute::_('index.php?option=com_goals&task=plan.delete&id='.$plan->id); ?>')
                                                                    "><?php echo JText::_('COM_GOAL_PLAN_REMOVE_DELETE'); ?></a></li>
                                                	    </ul>
                                                	</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

					<?php }?>
    </ul>
				<?php } else {?>
					<div class="gl_msntf"><?php echo JText::_('COM_GOALS_PLANS_NOT_FOUND');?></div>
				<?php }?>


    <script type="text/javascript">
        function removeGoalFromFeatured(url) {
               window.location.href=url;
        }

        function removePlan(url) {

            if (confirm("<?php echo JText::_('COM_GOALS_REMOVE_PLAN_MESSAGE') ?>")) {
                window.location.href=url;
            }
        }

        </script>
</div>