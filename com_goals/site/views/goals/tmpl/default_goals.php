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
					  		$goal = $items[$i];

                            $procent = (($goal->percent)>100)?100:$goal->percent;

				  ?>

								 <li class="goals-list-layout">
                                     <div class="goals-drag">
                                        <div class="goals-item clearfix" style="border-left: 5px solid <?php echo $goal->status; ?>">
                                            <div class="goals-left">
                                                <div class="goals-star star-active <?php echo (!$goal->featured)?'star-nonfeatured':'star-featured'; ?>">
                                                    <a href="<?php echo JRoute::_('index.php?option=com_goals&task=goal.featuredOnOff&id='.$goal->id); ?>" title="<?php echo (!$goal->featured)?'Add to featured':'Remove from featured'; ?>">&#x2605;</a>
                                                </div>
                                                <h3 class="gl-goal-title">
                                                    <?php
                                                        $glink = JRoute::_('index.php?option=com_goals&view=goal&id='.$goal->id.$tmpl);
                                                        echo '<a href="'.$glink.'">'.$goal->title.'</a>';
                                                    ?>
                                                </h3>
                                                <div class="gl-goal-short-details">
                                                     <div class="goals-state">
                                                    <?php


                                                    if (isset($goal->records_count)) {
                                                        $rlink = JRoute::_('index.php?option=com_goals&view=records&gid=' . (int)$goal->id . $tmpl);
                                                        echo '<a href="' . $rlink . '" title="" class="gl_task_count">' . JText::_('COM_GOALS_TASKS') . ' (' . (int)$goal->records_count . ')</a>';
                                                    }
                                                    if (isset($goal->milistones_count)) {
                                                        $rlink = JRoute::_('index.php?option=com_goals&view=milistones&gid=' . (int)$goal->id . $tmpl);
                                                        echo '<a href="' . $rlink . '" title="" class="gl_task_count">' . JText::_('COM_GOALS_MILISTONES') . ' (' . (int)$goal->milistones_count . ')</a>';
                                                        echo '</span>';
                                                    }
    ?>                                              </div>

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
                                                	<div class="progress progress-small progress-striped">
                                                	    <div class="bar" style="width:<?php echo $procent?>%; background-color: <?php echo $goal->status?>"></div>
                                                	</div>
                                                	<div class="progressbar_label_right"><?php echo $procent;?>%</div>
                                                </div>

                                                <div class="clr"></div>
                                            </div>
                                            <div class="goal-item-actions goals-right-hover">
                                                <div class="goals-state">
                                                    <a class="state-link"
                                                        href="<?php echo JRoute::_('index.php?option=com_goals&view=editrecord&gid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ADD_RECORD')?></a>
                                                    <a class="state-link"
                                                        href="<?php echo JRoute::_('index.php?option=com_goals&view=editmilistone&gid='.$goal->id); ?>"><?php echo JText::_('COM_GOALS_ADD_MILESTONE')?></a>

                                                </div>
                                                <div class="goals-edit-item">
                                                	<a class="goals-diagr" title="" href="<?php echo $glink ?>"><img src="components/com_goals/assets/images/diagr.png"       alt=""/></a>
                                                	<div class="btn-group">
                                                	    <a class="btn" href='<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'editgoal','id'=>$goal->id))); ?>'><?php echo JText::_('COM_GOAL_GOAL_EDITBUTTON'); ?></a>
                                                	    <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                	        <span class="caret"></span>
                                                	    </button>
                                                	    <ul class="dropdown-menu">
                                                	        <li><a onclick="removeGoalFromFeatured('<?php echo JRoute::_('index.php?option=com_goals&task=goal.featuredOnOff&id='.$goal->id); ?>')" href="javascript:void(0)"><?php echo ($goal->featured)?JText::_('COM_GOAL_GOAL_REMOVE_FROM_FEATURED'):JText::_('COM_GOAL_GOAL_ADD_TO_FEATURED'); ?></a>
                                                	        </li>
                                                	        <li><a onclick="removeGoal('<?php echo JRoute::_('index.php?option=com_goals&task=goal.delete&id='.$goal->id); ?>')" href="javascript:void(0)"><?php echo JText::_('COM_GOAL_GOAL_REMOVE_DELETE'); ?></a></li>
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
					<div class="gl_msntf"><?php echo JText::_('COM_GOALS_NOT_FOUND');?></div>
				<?php }?>


    <script type="text/javascript">
        function removeGoalFromFeatured(url) {


               window.location.href=url;

        }

        function removeGoal(url) {

            if (confirm("<?php echo JText::_('COM_GOALS_REMOVE_GOAL_MESSAGE') ?>")) {
                window.location.href=url;
            }
        }

        </script>
</div>