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
<div id="goals-wrap" class="gl_dashboard">

    <!--goals-list-->
    <div class="goals-content goals-today">
            
            <div class="goals-today-item">
                <h3 class="style-like-navi"><?php echo JText::_('COM_GOALS_TODAYS_RECORDS') ?><span class="badge badge-info badge-large"><?php echo $this->count_todayrecords ?></span></h3>
                <form action="<?php echo JRoute::_('index.php')?>" >
                    <input type="hidden" name="option" value="com_goals" />
                    <input type="hidden" name="view" value="editrecord" />
                <div class="goals-action-panel form-horizontal">
                	<div class="control-group">
                		<select name="gid" id="gid_form" onchange="jQuery('#mk_record_submit').removeAttr('disabled');">
                		    <option disabled selected><?php echo JText::_('COM_GOALS_TODAY_SELECT_GOAL') ?></option>
                            <?php
                            foreach($this->goals as $goal) {
                                echo '<option value="'.$goal->id.'">'.$goal->title.'</option>';
                            }
                            ?>
                		</select>

                        <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid')?>" />
                		<button class="btn" type="submit" id="mk_record_submit" disabled="disabled"><?php echo JText::_('COM_GOALS_TODAY_MAKE_RECORD') ?></button>
                </form>
                	</div>
                </div>

                <ul class="goals-formedlist">               

                <?php
                    foreach ($this->records as $record) {

                         ?>
                            <li>
                                <div class="goals-drag clearfix">
                                    <div class="goals-left">
                                        <h3><a href="<?php echo JRoute::_('index.php?option=com_goals&view=record&id='.$record->id) ?>"><?php echo $record->title ?></a></h3>
                                        <div class="goals-name">
                                            <span class="goals-grey"><?php echo JText::_('COM_GOALS_TODAY_GOAL') ?>: </span><span class="goals-underline"><a href="<?php echo JRoute::_('index.php?option=com_goals&view=goal&id='.$record->gid) ?>"><?php echo $record->gtitle ?></a></span>
                                        </div>
                                    </div>
                                    <div class="goals-right">
                                        <span class="goals-grey"><?php echo JText::_('COM_GOALS_TODAY_LOGGED_AT') ?>: </span><span class="goals-underline"><?php echo date('H:m',strtotime($record->date)) ?></span>
                                        <span class="goals-grey"><?php echo JText::_('COM_GOALS_TODAY_RESULT') ?>: </span><span class="goals-underline"><?php echo $record->value.' '.$record->gmetric ?></span>
                                    </div>
                                </div>
                            </li>

                            <?php
                    }


                    ?>
                    <?php if (!count($this->records)) echo '<li>'.JText::_('COM_GOALS_TODAY_NO_RECORDS').'</li>'; ?>
                </ul>
            </div>
            
            <div class="goals-today-item tabbable">
                <ul class="goals-toggle nav nav-tabs">
                    <li class="active">
                    	<a href="#duetoday" class="header-in-tab" data-toggle="tab"><?php echo JText::_('COM_GOALS_TODAY_TASK_DUE_TODAY') ?><span class="badge badge-info badge-large"><?php echo count($this->tasks)?></span></a>
                    </li>
                    <li>
                    	<a href="#overdue" class="header-in-tab" data-toggle="tab"><?php echo JText::_('COM_GOALS_TODAY_OVERDUE') ?> <span class="badge badge-info badge-info_overdue badge-large"><?php echo count($this->overdue_tasks)?></span></a>
                    </li>


                </ul>
                <div class="tab-content">
                    <ul class="goals-formedlist goals-toggle-list goals-task tab-pane active" id="duetoday">
                        <?php foreach ($this->tasks as $task) {

                        ?>
                        <li>
                            <div class="goals-drag clearfix">
                                <div class="goals-left">
                                    <h3><a href="<?php echo JRoute::_('index.php?option=com_goals&view=plantask&id='.$task->id) ?>"><?php echo $task->title ?></a></h3>
                                    <div class="goals-name">
                                        <span class="goals-grey"><?php echo JText::_('COM_GOALS_TODAY_PLAN') ?>: </span><span class="goals-underline"><a href="<?php echo JRoute::_('index.php?option=com_goals&view=plan&id='.$task->pid) ?>"><?php echo $task->ptitle ?></a></span>
                                        <span class="goals-grey"><?php echo JText::_('COM_GOALS_TODAY_STAGE') ?>: </span><span class="goals-underline"><a href="<?php echo JRoute::_('index.php?option=com_goals&view=stages&id='.$task->pid) ?>"><?php echo $task->stitle ?></a></span>
                                    </div>
                                </div>
                                <div class="goals-right goals-right-task">
                                    <?php if (!$task->status) { ?>
                                    <div class="check-task check-task-task checkbox_off">
                                        <input type="checkbox" class="check-done" id="check-done<?php echo $task->id ?>" name="done" value="no" >
                                    </div>
                                    <label for="check-done<?php echo $task->id ?>"><?php echo JText::_('COM_GOALS_TODAY_TASK_TO_BE_DONE') ?></label>
                                        <?php } else { ?>
                                    <div class="check-task check-task-task checkbox_on">
                                        <input type="checkbox" class="check-done" id="check-done<?php echo $task->id ?>" name="done" value="yes" checked="checked" >
                                    </div>
                                    <label for="check-done<?php echo $task->id ?>" class="goals-task-green"><?php echo JText::_('COM_GOALS_TODAY_TASK_ACCOMPLISHED') ?></label>
                                    <?php } ?>

                                </div>
                            </div>
                        </li>


                        <?php } ?>
                        <?php if (!count($this->tasks)) echo '<li>'.JText::_('COM_GOALS_TODAY_NO_TASKS').'</li>'; ?>
                    </ul>

                    <ul class="goals-formedlist  goals-toggle-list goals-overdue tab-pane" id="overdue">

                        <?php foreach($this->overdue_tasks as $ov_task) {
                        $left = GoalsHelper::date_diff(date('Y-m-d', time()), $ov_task->date);
                        $leftstr = GoalsHelper::getDateLeft($left);

                        ?>
                        <li>
                            <div class="goals-drag clearfix">
                                <div class="goals-left">
                                    <h3><?php echo $ov_task->title ?></h3>
                                    <div class="goals-name">
                                        <span class="goals-grey"><?php echo JText::_('COM_GOALS_TODAY_PLAN') ?>: </span><span class="goals-underline"><a href="<?php echo JRoute::_('index.php?option=com_goals&view=plan&id='.$ov_task->pid) ?>"><?php echo $ov_task->ptitle ?></a></span>
                                        <span class="goals-grey"><?php echo JText::_('COM_GOALS_TODAY_STAGE') ?>: </span><span class="goals-underline"><a href="<?php echo JRoute::_('index.php?option=com_goals&view=stages&id='.$ov_task->pid) ?>"><?php echo $ov_task->stitle ?></a></span>
                                    </div>
                                </div>                             	
                                <div class="goals-right">
                                    <div class="check-task check-task-task checkbox_off">
                                        <input type="checkbox" class="check-done" id="check-done<?php echo $ov_task->id ?>" name="done" value="no">
                                    </div>
                                    <label for="check-done<?php echo $ov_task->id ?>" class="goals-overdue-red"><?php echo $leftstr ?></label>
                                </div>	
                            </div>
                        </li>	             
                     <?php } ?>
                     <?php if (!count($this->overdue_tasks)) echo '<li>'.JText::_('COM_GOALS_TODAY_NO_OVERDUE_TASKS').'</li>'; ?>
                    </ul>                    
                </div>
            </div>
            
            <div class="goals-today-item">
                <h3 class="style-like-navi"><?php echo JText::_('COM_GOALS_TODAY_HABITS')?><span class="badge badge-info badge-large"><?php echo count($this->habits) ?></span></h3>

                <ul class="goals-formedlist goals-habit-fortoday">
                    <?php foreach($this->habits as $habit) { ?>
                    <?php

                    switch ( $habit->type )
                    {
                        case '+': $status_style = 'good'; break;
                        default:  $status_style = 'bad';break;
                    }

                    $procent = $habit->percent;

                    ?>


                    <li>
                        <div class="goals-drag clearfix">
                            <div class="goals-left">
                                <h3><?php echo $habit->title;?></h3>
                            </div>  
                            <div class="right-wrapper clearfix">
                                <div class="goals-checkblock">

                                        <?php if ($habit->todaydid===false) {?>
                                    <span class="check-task check-task-habits checkbox_off_<?php echo $status_style ?>">
                                        <input type="checkbox" class="check-done" id="check-hab<?php echo $habit->id;?>" name="check-hab<?php echo $habit->id;?>" value="no">
                                        <?php } else { ?>
                                    <span class="check-task check-task-habits checkbox_on_<?php echo $status_style ?>">
                                        <input type="checkbox" class="check-done" id="check-hab<?php echo $habit->id;?>" name="check-hab<?php echo $habit->id;?>" value="yes">
                                            <?php } ?>
                                    </span>
                                    <label class="checkbox" for="check-hab<?php echo $habit->id;?>">
                                        <?php echo $habit->complete_count.' '.JText::_('COM_GOALS_TODAY_HABIT_CHECKS');   ?>
                                    </label>
                                </div>
                                <div class="goals-right todays-habits">
                                    <div class="goal-item-progress">
                                        <div class="progress progress-small progress-danger progress-striped">
                                            <div class="bar" style="width:<?php echo $procent?>%; background-color: #62C462"></div>
                                        </div>
                                        <div class="progressbar_label_right"><?php echo $procent?>%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li> 
                       <?php } ?>
                    <?php if (!count($this->habits)) echo '<li>'.JText::_('COM_GOALS_TODAY_NO_HABITS').'</li>'; ?>
                </ul>
                    
            </div>



 <script>
jQuery(document).ready(function(){
     /*today - task checkboxes*/
    jQuery(".check-done").click(function(){
        var arrId = jQuery(this).attr('id').split('check-done');

        if (jQuery(this).is(":checked")) {
            jQuery(this).parents(".check-task-task").removeClass("checkbox_off").addClass("checkbox_on");
            // change label color in tasks:
            jQuery(this).parents(".goals-right-task").find("label").addClass("goals-task-green").html("accomplished");
            jQuery(this).attr("value", "yes");
            change_task_status(arrId[1],1)
        }
    
        else {
            jQuery(this).parents(".check-task-task").removeClass("checkbox_on").addClass("checkbox_off");
            jQuery(this).parents(".goals-right-task").find("label").removeClass("goals-task-green").html("to be done");
            jQuery(this).attr("value", "no");
            change_task_status(arrId[1],0);

        }
                
    });

    /*today - habits for today checkboxes*/
    jQuery(".check-task-habits").click(function(){
        var arrId = jQuery(this).parent().find('input').attr('id').split('check-hab');
        if(jQuery(this).hasClass("checkbox_off_good")){
            jQuery(this).addClass("checkbox_on_good").removeClass("checkbox_off_good");
            change_habit_status(arrId[1],1,jQuery(this).parent().find('input').attr('id'));
        }
        else {
            if(jQuery(this).hasClass("checkbox_on_good")){
             jQuery(this).addClass("checkbox_off_good").removeClass("checkbox_on_good");
             change_habit_status(arrId[1],0,jQuery(this).parent().find('input').attr('id'));

        }
            else{
                if(jQuery(this).hasClass("checkbox_off_bad")){
                jQuery(this).addClass("checkbox_on_bad").removeClass("checkbox_off_bad");
                    change_habit_status(arrId[1],1,jQuery(this).parent().find('input').attr('id'));
            }
                else{
                    if(jQuery(this).hasClass("checkbox_on_bad")){
                    jQuery(this).addClass("checkbox_off_bad").removeClass("checkbox_on_bad");
                        change_habit_status(arrId[1],0,jQuery(this).parent().find('input').attr('id'));
                }}
            }
        }

    });
    
});
function change_habit_status(hid,type,container)
{
    if (hid)
    {
        var url="<?php echo JURI::root()?>/index.php?option=com_goals&task=habit.addstatus&tmpl=component";
        var dan="hid=" + hid + "&t="+ type;

        var myAjax = new Request.HTML({
            url:url,
            method: "post",
            data: dan,
            encoding:"utf-8",
            onComplete:function(responce){

              resp_array =  responce[0].textContent.split(';');
              jQuery('#'+container).parent().parent().find('label').html(resp_array[0] + ' <?php echo JText::_('COM_GOALS_TODAY_HABIT_CHECKS') ?>');
              jQuery('#'+container).parent().parent().parent().find('div.bar').css('width',resp_array[1] + '%');
                jQuery('#'+container).parent().parent().parent().find('div.progressbar_label_right').html(resp_array[1] + '%');
            }
        });
        myAjax.send();
    }
}

function change_task_status(hid,type)
{
    if (hid)
    {
        var url="<?php echo JURI::root()?>/index.php?option=com_goals&task=plantask.addstatus&tmpl=component";
        var dan="hid=" + hid + "&t="+ type;
        var statusdiv =  $('habit_stat_'+hid);
        var myAjax = new Request.HTML({
            url:url,
            method: "post",
            data: dan,
            encoding:"utf-8",
            update: statusdiv

        });
        myAjax.send();
    }
}
 </script>
 
    </div>
 </div>