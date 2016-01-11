<?php
/**
* Goals EasySocial widget for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );


class GoalsmanagerWidgetsProfile extends SocialAppsWidgets
{
    /**
     * This will display a simple "Hello World" message on the person's profile sidebar.
     *
     * @param   SocialUser  The user object.
     * @return  null
     */
    public function sidebarTop( $user )
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');

        require_once(JPATH_BASE . '/components/com_goals/helpers/goals.php');

        // for current viewed $user
        // for viewer $viewer = Foundry::user();

        $this->user = $user;

        JFactory::getLanguage()->load( 'com_goals' , JPATH_ROOT );

        $vprefix = 'dashboard';

        // Load up the model
        $model 	= $this->getModel( $vprefix );

        require_once(JPATH_BASE . '/components/com_goals/views/'.$vprefix.'/view.html.php');

        $vclass = 'GoalsView'.$vprefix;
        $view = new $vclass();

        // Get the list of goals created by the user.
        // result is set object of data
        $result = $model->getItems( $user->id );
        $pagination = $model->getPagination();

        // result is set array of data
        $data = $view->prepareData($result);

        //render start
        if($data->goals_array || $data->plans_array) $this->renderScript();
        ?>
        <div id="goals-widget" class="es-widget">
		<?php
        if($data->goals_array){
            echo '<div class="es-widget-head"><div class="pull-left widget-title">';
            if($user->isViewer()){
                echo JText::_('COM_GOALS_WIDGET_TITLE_MYFEATURED_GOALS');
            }else{
                echo JText::_('COM_GOALS_WIDGET_TITLE_FEATURED_GOALS');
            }
            echo '</div></div>';
            echo '<div class="es-widget-body">';
                echo '<div class="goals-list goals-widget">';
                    foreach($data->goals_array as $goal)
                    {
                        $goal->percent = (($goal->percent)>100)?100:$goal->percent;
                        if($user->isViewer()){
                            $this->renderGoal($goal,'','widget','own');
                        }else if($user->isFriends( Foundry::user()->id)){
                            $this->renderGoal($goal,$user,'widget','friend');
                        }else{
                            $this->renderGoal($goal,$user,'widget');
                        }
                    }
                echo '</div>';
            echo '</div>';
        }

        if($data->plans_array){
            echo '<div class="es-widget-head"><div class="pull-left widget-title">';
            if($user->isViewer()){
                echo JText::_('COM_GOALS_WIDGET_TITLE_MYFEATURED_PLANS');
            }else{
                echo JText::_('COM_GOALS_WIDGET_TITLE_FEATURED_PLANS');
            }
            echo '</div></div>';
            echo '<div class="es-widget-body">';
                echo '<div class="goals-list goals-widget">';
                    foreach($data->plans_array as $plan)
                    {
                        $plan->percent = (($plan->percent)>100)?100:$plan->percent;
                        if($user->isViewer()){
                            $this->renderPlan($plan, '', 'widget', 'own');
                        }else if($user->isFriends( Foundry::user()->id )){
                            $this->renderPlan($plan, $user,'widget', 'friend');
                        }else{
                            $this->renderPlan($plan, $user,'widget');
                        }
                    }
                echo '</div>';
            echo '</div>';
        }

        //render end
        ?>
        </div>
        <?php

    }

    public function renderScript(){
        ?>
        <script>
            function easyPopUpComponent(url){
                EasySocial.dialog({
                    content: "\<div id='goals'\>\<img src='/media/com_easysocial/images/loading.gif'\/\>\<\/div\>"
                });
                setTimeout(function(){
                    jQuery('.es.modal.es-dialog #goals' ).load( url+'&tmpl=component&ranged=true #goals-wrap' );
                    setTimeout(function(){
                        var dialog = jQuery('.es.modal.es-dialog');
                        dialog.animate({top: "-="+dialog.height()/2, left: "-="+dialog.width()/2});
                    },1600);
                },600);
            }
			
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
        <?php
    }

    public function renderGoal($item, $userObj, $style='widget', $whois = ''){

        $date_format = JText::_('DATE_FORMAT_LC3');
        $tmpl = '';
        if(!$userObj) $userObj = JFactory::getUser();
        $user = '&user='.$userObj->id;

        if($item){
            if($style = 'widget'){
                ?>
                <div class="goals-list-layout">
                    <div class="goals-drag">
                        <div class="goals-item clearfix" style="border-left: 5px solid <?php echo $item->status; ?>">
                            <div>
                                <div class="goals-star star-active <?php echo (!$item->featured)?'star-nonfeatured':'star-featured'; ?>">
                                    <?php
                                    if($whois=='own' || $whois=='friend'){
                                        ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_goals&task=goal.featuredOnOff&id='.$item->id); ?>" title="<?php echo (!$item->featured)?'Add to featured':'Remove from featured'; ?>">&#x2605;</a>
                                    <?php
                                    }else{
                                        ?>
                                        <span >&#x2605;</span>
                                    <?php
                                    }
                                    ?>
                                    <?php /* ?><small>[<?php echo JText::_('COM_GOALS_GOAL'); ?>]</small> <?php */ ?>
                                    <?php
                                    if($whois=='own' || $whois=='friend'){
                                        ?>
                                        <?php
                                        $glink = JRoute::_('index.php?option=com_goals&view=goal&id='.$item->id.$tmpl.$user);
                                        echo '<a onclick="easyPopUpComponent(\''.$glink.'\')" href="javascript:void(0)">'.$item->title.'</a>';
                                        ?>

                                    <?php
                                    }else{
                                        ?>
                                        <span ><?php echo $item->title; ?></span>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                if($whois=='own' || $whois=='friend'){
                                    ?>
                                    <div class="gl-goal-short-details">
                                        <div class="goals-state">
                                            <?php
                                            if (isset($item->records_count)) {
                                                $rlink = JRoute::_('index.php?option=com_goals&view=records&gid=' . (int)$item->id.$tmpl.$user);
                                                echo '<a  onclick="easyPopUpComponent(\''.$rlink.'\')" href="javascript:void(0)" title="" class="gl_task_count">' . JText::_('COM_GOALS_TASKS') . ' (' . (int)$item->records_count . ')</a>';
                                            }
                                            if (isset($item->milistones_count)) {
                                                $rlink = JRoute::_('index.php?option=com_goals&view=milistones&gid=' . (int)$item->id.$tmpl.$user);
                                                echo '<a  onclick="easyPopUpComponent(\''.$rlink.'\')" href="javascript:void(0)" title="" class="gl_task_count">' . JText::_('COM_GOALS_MILISTONES') . ' (' . (int)$item->milistones_count . ')</a>';
                                                echo '</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="goal-item-progress">
                                    <div class="progress progress-small progress-striped">
                                        <div class="bar" style="width:<?php echo $item->percent?>%; background-color: <?php echo $item->status?>"></div>
                                    </div>
                                    <div class="progressbar_label_right"><?php echo $item->percent;?>%</div>
                                </div>
                                <div class="gl-goal-short-details">
                                    <div class="goals-date">
                                        <?php

                                        if (isset($item->deadline))
                                            if ($item->deadline != '0000-00-00 00:00:00') {
                                                echo '<span class="date-by">' . JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $item->deadline, $date_format) . '</span>';
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php
                                if($whois=='own'){
                                    ?>
                                    <div class="goal-item-actions">
                                        <div class="goals-edit-item">
                                            <div class="btn-group">
                                                <button class="btn goals-diagr" onclick="location.href='<?php echo $glink ?>'"><img src="components/com_goals/assets/images/diagr.png"       alt=""/></button>
                                                <button class="btn" onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editgoal&id='.$item->id) ?>'"><?php echo JText::_('COM_GOAL_GOAL_EDITBUTTON'); ?></button>
                                                <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="state-link"  href="<?php echo JRoute::_('index.php?option=com_goals&view=editrecord&gid='.$item->id); ?>"><?php echo JText::_('COM_GOALS_ADD_RECORD')?></a>
                                                    </li>
                                                    <li>
                                                        <a class="state-link" href="<?php echo JRoute::_('index.php?option=com_goals&view=editmilistone&gid='.$item->id); ?>"><?php echo JText::_('COM_GOALS_ADD_MILESTONE')?></a>
                                                    </li>
                                                    <li>
                                                        <a onclick="removeGoalFromFeatured('<?php echo JRoute::_('index.php?option=com_goals&task=goal.featuredOnOff&id='.$item->id); ?>')" href="javascript:void(0)"><?php echo ($item->featured)?JText::_('COM_GOAL_GOAL_REMOVE_FROM_FEATURED'):JText::_('COM_GOAL_GOAL_ADD_TO_FEATURED'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a onclick="removeGoal('<?php echo JRoute::_('index.php?option=com_goals&task=goal.delete&id='.$item->id); ?>')" href="javascript:void(0)"><?php echo JText::_('COM_GOAL_GOAL_REMOVE_DELETE'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="clr"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else{
            return false;
        }
    }

    public function renderPlan($item, $userObj, $style='widget', $whois = ''){

        $date_format = JText::_('DATE_FORMAT_LC3');
        $tmpl = '';
        if(!$userObj) $userObj = JFactory::getUser();
        $user = '&user='.$userObj->id;

        if($item){
            if($style = 'widget'){
                ?>
                <div class="goals-list-layout">
                    <div class="goals-drag">
                        <div class="goals-item clearfix" style="border-left: 5px solid <?php echo $item->status; ?>">
                            <div>
                                <div class="goals-star star-active <?php echo (!$item->featured)?'star-nonfeatured':'star-featured'; ?>">
                                    <?php
                                    if($whois=='own' || $whois=='friend'){
                                        ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_goals&task=plan.featuredOnOff&id='.$item->id); ?>" title="<?php echo (!$item->featured)?'Add to featured':'Remove from featured'; ?>">&#x2605;</a>
                                    <?php
                                    }else{
                                        ?>
                                        <span >&#x2605;</span>
                                    <?php
                                    }
                                    ?>
                                    <?php /* ?><small>[<?php echo JText::_('COM_GOALS_PLAN'); ?>]</small> <?php */ ?>
                                    <?php
                                    if($whois=='own' || $whois=='friend'){
                                        ?>
                                        <?php
                                        $glink = JRoute::_('index.php?option=com_goals&view=plan&id='.$item->id.$tmpl.$user);
                                        echo '<a  onclick="easyPopUpComponent(\''.$glink.'\')" href="javascript:void(0)">'.$item->title.'</a>';
                                        ?>
                                    <?php
                                    }else{
                                        ?>
                                        <span ><?php echo $item->title; ?></span>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                if($whois=='own' || $whois=='friend'){
                                    ?>
                                    <div class="gl-goal-short-details">
                                        <div class="goals-state">
                                            <?php
                                            if (isset($item->plantasks_count)) {
                                                $rlink = JRoute::_('index.php?option=com_goals&view=plantasks&pid=' . (int)$item->id.$tmpl.$user);
                                                echo '<a  onclick="easyPopUpComponent(\''.$rlink.'\')" href="javascript:void(0)" title="" class="gl_task_count">' . JText::_('COM_GOALS_PLANTASKS') . ' (' . (int)$item->plantasks_count . ')</a>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="goal-item-progress">
                                    <div class="progress progress-small progress-striped">
                                        <div class="bar" style="width:<?php echo $item->percent?>%; background-color: <?php echo $item->status?>"></div>
                                    </div>
                                    <div class="progressbar_label_right"><?php echo $item->percent;?>%</div>
                                </div>
                                <div class="gl-goal-short-details">
                                    <div class="goals-date">
                                        <?php

                                        if (isset($item->deadline))
                                            if ($item->deadline != '0000-00-00 00:00:00') {
                                                echo '<span class="date-by">' . JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $item->deadline, $date_format) . '</span>';
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php
                                if($whois=='own'){
                                    ?>
                                    <div class="goal-item-actions">
                                        <div class="goals-edit-item">
                                            <div class="btn-group">
                                                <button class="btn goals-diagr" onclick="location.href='<?php echo $glink ?>'"><img src="components/com_goals/assets/images/diagr.png"       alt=""/></button>
                                                <button class="btn" onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editplan&id='.$item->id) ?>'"><?php echo JText::_('COM_GOAL_PLAN_EDITBUTTON'); ?></button>
                                                <button class="btn dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="state-link"  href="<?php echo JRoute::_('index.php?option=com_goals&view=editplantask&pid='.$item->id); ?>"><?php echo JText::_('COM_GOALS_ADD_TASK')?></a>
                                                    </li>
                                                    <li>
                                                        <a class="state-link" href="<?php echo JRoute::_('index.php?option=com_goals&view=editstage&pid='.$item->id); ?>"><?php echo JText::_('COM_GOALS_ADD_STAGE')?></a>
                                                    </li>
                                                    <li>
                                                        <a onclick="removeGoalFromFeatured('<?php echo JRoute::_('index.php?option=com_goals&task=plan.featuredOnOff&id='.$item->id); ?>')" href="javascript:void(0)"><?php echo ($item->featured)?JText::_('COM_GOAL_GOAL_REMOVE_FROM_FEATURED'):JText::_('COM_GOAL_GOAL_ADD_TO_FEATURED'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a onclick="removePlan('<?php echo JRoute::_('index.php?option=com_goals&task=plan.delete&id='.$item->id); ?>')" href="javascript:void(0)"><?php echo JText::_('COM_GOAL_PLAN_REMOVE_DELETE'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="clr"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else{
            return false;
        }
    }
}