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

<script type="text/javascript">

    function change_habit_status(hid, type) {

        if (hid) {

            var url = "<?php echo JURI::root()?>/index.php?option=com_goals&task=habit.addstatus&tmpl=component";

            var dan = "hid=" + hid + "&t=" + type;

            var statusdiv = $('habit_stat_' + hid);

            var myAjax = new Request.HTML({

                url:url,

                method:"post",

                data:dan,

                encoding:"utf-8",

                update:statusdiv



            });

            myAjax.send();

        }

    }

</script>

<div class="goals-content">

    <div class="goals-calendar-week-holder">

        <ul class="goals_manager_calend">

            <?php

            $daysHeader = GoalsHelper::getHabitsDaysHeader($this->params->weak_day_start);

            foreach($daysHeader as $day) { ?>

                <li class="goals_title_day"><?php echo $day ?></li>

                <?php } ?>

        </ul>

    </div>

    <ul class="goals-list goals-habits-list ui-sortable">

        <?php

        for ($i = 0, $n = sizeof($items); $i < $n; $i++) {

            $habit = $items[$i];

            if ($habit->complete) continue;





            $days = array();

            if ($habit->days) {

                $days = explode(',', $habit->days);

            }

            $jdate = new JDate('now');

            $nw = $jdate->dayofweek;

            $week = $jdate->dayofweek;



            if ($nw == 0) $nw = 7;

            $daysTrigger = ($daysHeader==='s')?$nw:($nw - 1);

            $monday_date = date('Y-m-d', strtotime(date('Y-m-d', $this->startDate)) - ($daysTrigger) * 24 * 60 * 60);

            ?>

            <li class="goals-list-layout" id="habit_<?php echo $habit->id;?>">

                <div class="goals-drag">

                    <!--                        <div class="goals-drag-pict"></div>-->

                    <div class="goals-item goals-item-habbit clearfix">

                        <div class="gl_goal_status_left_part">

                            <?php

                            switch ($habit->type) {

                                case '+':

                                    $status_style = 'positive';

                                    break;

                                default:

                                    $status_style = 'negative';

                                    break;

                            }





                            $procent = $habit->percent;

                            $procent = ($procent > 100) ? 100 : $procent;

                            if ($procent < 15) $pstyle = 'red';

                            else

                                if ($procent < 50) $pstyle = 'yellow';

                                else

                                    if ($procent < 80) $pstyle = 'blue';

                                    else

                                        if ($procent < 98) $pstyle = 'pink';

                                        else $pstyle = 'green';

                            ?>

                            <div class="gl_habit_progress">

                                <div class="goal-item-progress vertical-progress-bar">

                                    <div class="progress progress-small">

                                        <div class="bar"

                                             style="height:<?php echo $procent?>%; background-color: <?php //echo $goal->status ?>"></div>

                                    </div>

                                    <div class="progressbar_label_right"><?php echo $procent;?>%</div>

                                </div>

                            </div>

                        </div>

                        <div class="gl_goal_left_part">

                            <div class="gl_goal_title">

                                <a href="<?php echo JRoute::_('index.php?option=com_goals&view=habithistory&id=' . (int)$habit->id . $tmpl);?>"><?php echo $habit->title;?></a>

                            </div>

                            <div class="goals_habits_started_on muted"><?php echo JText::_('COM_GOALS_HABITS_STARTED_ON') . ' ' . JHtml::_('date', $habit->date, JText::_('DATE_FORMAT_LC3')); ?></div>

                        </div>

                        <div class="goal-habit-actions">

                            <ul class="goals_manager_calend style_<?php echo $status_style;?>">

                                <li class="goals_manager_day<?php echo GoalsHelper::getRequiredDayClass((int)$habit->id, $monday_date); ?>"  <?php echo GoalsHelper::isDisabled($habit->date, $monday_date) ?>>

                                    <label for="monday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_day_name">Mon</label>

                                    <input type="checkbox" id="monday_<?php echo (int)$habit->id . $tmpl ?>"

                                           value="<?php echo $monday_date ?>"

                                           name="monday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_checkbox" <?php echo GoalsHelper::getCheckedHabit((int)$habit->id, $monday_date) ?> />



                                    <div class="goals_manager_checkbox_custom goals_manager_<?php echo $status_style;?>"></div>

                                </li>

                                <li class="goals_manager_day<?php echo GoalsHelper::getRequiredDayClass((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 1 * 24 * 60 * 60)); ?>" <?php echo GoalsHelper::isDisabled($habit->date, date('Y-m-d', strtotime($monday_date) + 1 * 24 * 60 * 60)) ?>>

                                    <label for="tuesday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_day_name">Tue</label>

                                    <input type="checkbox" id="tuesday_<?php echo (int)$habit->id . $tmpl ?>"

                                           value="<?php echo date('Y-m-d', strtotime($monday_date) + 1 * 24 * 60 * 60) ?>"

                                           name="tuesday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_checkbox" <?php echo GoalsHelper::getCheckedHabit((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 1 * 24 * 60 * 60)) ?> />



                                    <div class="goals_manager_checkbox_custom goals_manager_<?php echo $status_style;?>"></div>

                                </li>

                                <li class="goals_manager_day<?php echo GoalsHelper::getRequiredDayClass((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 2 * 24 * 60 * 60)); ?>" <?php echo GoalsHelper::isDisabled($habit->date, date('Y-m-d', strtotime($monday_date) + 2 * 24 * 60 * 60)) ?>>

                                    <label for="wednesday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_day_name">Wed</label>

                                    <input type="checkbox" id="wednesday_<?php echo (int)$habit->id . $tmpl ?>"

                                           value="<?php echo date('Y-m-d', strtotime($monday_date) + 2 * 24 * 60 * 60) ?>"

                                           name="wednesday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_checkbox" <?php echo GoalsHelper::getCheckedHabit((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 2 * 24 * 60 * 60)) ?> />



                                    <div class="goals_manager_checkbox_custom goals_manager_<?php echo $status_style;?>"></div>

                                </li>

                                <li class="goals_manager_day<?php echo GoalsHelper::getRequiredDayClass((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 3 * 24 * 60 * 60)); ?>" <?php echo GoalsHelper::isDisabled($habit->date, date('Y-m-d', strtotime($monday_date) + 3 * 24 * 60 * 60)) ?>>

                                    <label for="thursday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_day_name">Thu</label>

                                    <input type="checkbox" id="thursday_<?php echo (int)$habit->id . $tmpl ?>"

                                           value="<?php echo date('Y-m-d', strtotime($monday_date) + 3 * 24 * 60 * 60) ?>"

                                           name="thursday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_checkbox" <?php echo GoalsHelper::getCheckedHabit((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 3 * 24 * 60 * 60)) ?> />



                                    <div class="goals_manager_checkbox_custom goals_manager_<?php echo $status_style;?>"></div>

                                </li>

                                <li class="goals_manager_day<?php echo GoalsHelper::getRequiredDayClass((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 4 * 24 * 60 * 60)); ?>" <?php echo GoalsHelper::isDisabled($habit->date, date('Y-m-d', strtotime($monday_date) + 4 * 24 * 60 * 60)) ?>>

                                    <label for="friday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_day_name">Fri</label>

                                    <input type="checkbox" id="friday_<?php echo (int)$habit->id . $tmpl ?>"

                                           value="<?php echo date('Y-m-d', strtotime($monday_date) + 4 * 24 * 60 * 60) ?>"

                                           name="friday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_checkbox" <?php echo GoalsHelper::getCheckedHabit((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 4 * 24 * 60 * 60)) ?> />



                                    <div class="goals_manager_checkbox_custom goals_manager_<?php echo $status_style;?>"></div>

                                </li>

                                <li class="goals_manager_day<?php echo GoalsHelper::getRequiredDayClass((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 5 * 24 * 60 * 60)); ?>" <?php echo GoalsHelper::isDisabled($habit->date, date('Y-m-d', strtotime($monday_date) + 5 * 24 * 60 * 60)) ?>>

                                    <label for="saturday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_day_name">Sat</label>

                                    <input type="checkbox" id="saturday_<?php echo (int)$habit->id . $tmpl ?>"

                                           value="<?php echo date('Y-m-d', strtotime($monday_date) + 5 * 24 * 60 * 60) ?>"

                                           name="saturday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_checkbox" <?php echo GoalsHelper::getCheckedHabit((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 5 * 24 * 60 * 60)) ?> />



                                    <div class="goals_manager_checkbox_custom goals_manager_<?php echo $status_style;?>"></div>

                                </li>

                                <li class="goals_manager_day<?php echo GoalsHelper::getRequiredDayClass((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 6 * 24 * 60 * 60)); ?>" <?php echo GoalsHelper::isDisabled($habit->date, date('Y-m-d', strtotime($monday_date) + 6 * 24 * 60 * 60)) ?>>

                                    <label for="sunday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_day_name">Sun</label>

                                    <input type="checkbox" id="sunday_<?php echo (int)$habit->id . $tmpl ?>"

                                           value="<?php echo date('Y-m-d', strtotime($monday_date) + 6 * 24 * 60 * 60) ?>"

                                           name="sunday_<?php echo (int)$habit->id . $tmpl ?>"

                                           class="goals_manager_checkbox" <?php echo GoalsHelper::getCheckedHabit((int)$habit->id, date('Y-m-d', strtotime($monday_date) + 6 * 24 * 60 * 60)) ?>/>



                                    <div class="goals_manager_checkbox_custom goals_manager_<?php echo $status_style;?>"></div>

                                </li>

                            </ul>

                            <div class="goals-edit-item">

                                <div class="btn-group">

                                    <a class="btn" href='<?php echo JRoute::_('index.php?option=com_goals&view=edithabit&id=' . (int)$habit->id . $tmpl);?>')"><?php echo JText::_('COM_GOAL_HABIT_EDITBUTTON'); ?></a>

                                    <button class="btn dropdown-toggle" data-toggle="dropdown">

                                        <span class="caret"></span>

                                    </button>

                                    <ul class="dropdown-menu">

                                        <li><a href="javascript:void(0)"

                                               onclick="goalgoto('<?php echo JRoute::_('index.php?option=com_goals&task=habit.featuredOnOff&id=' . $habit->id); ?>')"><?php echo ($habit->featured) ? JText::_('COM_GOAL_GOAL_REMOVE_FROM_FEATURED') : JText::_('COM_GOAL_GOAL_ADD_TO_FEATURED'); ?></a>

                                        </li>

                                        <li><a href="javascript:void(0)"

                                               onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_HABIT_MESS'); ?>'))goalgoto('<?php echo JRoute::_('index.php?option=com_goals&task=habit.delete&id=' . (int)$habit->id . $tmpl);?>')"><?php echo JText::_('COM_GOALS_DELETE_HABIT'); ?></a>

                                        </li>

                                    </ul>

                                </div>

                            </div>

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

<?php } ?>

<?php if (sizeof($items)) { ?>

<div class="week-pagination">

	<a class="pgn-button pgn-prev" href="<?php echo $this->hrefBack ?>"><?php echo JText::_('COM_GOALS_HABITS_PREV_WEEK') ?></a>

	(<?php echo date('Y M d', strtotime($monday_date)) . ' - ' . date('Y M d', strtotime($monday_date) + 6 * 24 * 60 * 60) ?>)

	<a class="pgn-button pgn-next" href="<?php echo $this->hrefForward ?>"><?php echo JText::_('COM_GOALS_HABITS_NEXT_WEEK') ?></a>

</div>

<?php } ?>

<script type="text/javascript">

    jQuery(document).ready(function () {

        jQuery('.goals_manager_day input').click(function () {

            container = jQuery(this).attr('id');

            arrHabit = jQuery(this).attr('id').split('_');

            var url = "<?php echo JURI::root()?>/index.php?option=com_goals&task=habit.switchstatus&tmpl=component";

            var dan = "hid=" + arrHabit[1] + "&day=" + jQuery(this).attr('value');

            var myAjax = new Request.HTML({

                url:url,

                method:"post",

                data:dan,

                encoding:"utf-8",

                onComplete:function (responce) {

                    resp_array = responce[0].textContent.split(';');

                    if (resp_array[1] > 100) resp_array[1] = 100;

                    //jQuery('#'+container).parent().parent().parent().find('label').html(resp_array[0] + ' <?php //echo JText::_('COM_GOALS_TODAY_HABIT_CHECKS') ?>');

                    jQuery('#' + container).parent().parent().parent().parent().find('div.bar').css('height', resp_array[1] + '%');

                    jQuery('#' + container).parent().parent().parent().parent().find('div.progressbar_label_right').html(resp_array[1] + '%');

                }

            });

            myAjax.send();

        })

    });

    function goalgoto(url) {

        location.href = url;

    }

</script>