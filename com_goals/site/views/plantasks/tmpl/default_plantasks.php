<?php
/**
 * Goals component for Joomla 3.0
 * @package Goals
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/
defined('_JEXEC') or die('Restricted access');

//dummycode

$document = JFactory::getDocument();
$document->addScriptDeclaration('
jQuery(document).ready(function($){
    var pl = $("#plan'.$this->active['plan'].'");
    if(!pl.hasClass("in")){
        pl.addClass("in");
    }
    var st = $("#stage'.$this->active['stage'].'");
    if(!st.hasClass("in")){
        st.addClass("in");
    }
    var ts = $("#task'.$this->active['task'].'");
    if(!ts.hasClass("in")){
        ts.addClass("in");
    }
});
');

$items = $this->recs;
if(JRequest::get('sid')) $sid = '&sid=' . (int)JRequest::get('sid');
$tmpl = JRequest::getVar('tmpl');
if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';


?>
<?php
if(count($this->plans)){
    if(!$this->accordion['plans']){
        echo JHtml::_('bootstrap.startAccordion', 'plan-slide', array('active' => 'plan'.$this->active['plan']));
    }
    foreach($this->plans as $plan){
        if(!$this->accordion['plans']){
            echo JHtml::_('bootstrap.addSlide', 'plan-slide', $plan->title, 'plan'.$plan->id);
        }
        $own = false;
        if ($plan->uid == JFactory::getUser()->id) {
            $own = true;
        }
        ?>
        <div class="text-center">
            <div class="" style="margin: 10px 0px;">
                <div class="gl_goal_progress">
                    <div class="goal-item-progress">
                        <div class="progress progress-small progress-striped" style="height: 10px!important;margin: 0px;">
                            <div class="bar"
                                 style="width:<?php echo $plan->percents; ?>%; background-color: <?php echo $plan->status ?>"></div>
                        </div>
                        <div class="progressbar_label_right"><?php echo $plan->percents;?>%</div>
                    </div>
                </div>
                <div class="clr"></div>
            </div>
        </div>
        <?php if($own){ ?>
        <p>
            <a class="btn" href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'editstage','pid'=>$plan->id))); ?>"><?php echo JText::_('COM_GOALS_ADD_STAGE')?></a>
        </p>
        <?php } ?>
        <?php

        if(!$this->accordion['stages']){
            echo JHtml::_('bootstrap.startAccordion', $plan->id.'stage-slide', array('active' => 'stage'.$this->active['stage']));
        }

        if(count($plan->stages)){
            foreach($plan->stages as $stage){
                if(!$this->accordion['stages']){
                    $state = "";
                    if($stage->status) $state ="<span class='icon-signup'> </span> ";
                    //else $state = "<span class='icon-checkbox-unchecked'> </span> ";
                    else $state = "";
                    $tasks = JText::_("COM_GOALS_STAGE_STASKS_COUNT")." (";
                    if($stage->tasks_count['all']){
                        $tasks .= $stage->tasks_count['done'].'/'.$stage->tasks_count['all'].")";
                    }else{
                        $tasks .= $stage->tasks_count['all'].")";
                    }

                    echo JHtml::_('bootstrap.addSlide', $plan->id.'stage-slide', '<div class="row-fluid"><div class="span9">'.$state.JText::_($stage->title).'</div><div class="span3 text-right">'.$tasks.'</div></div>', 'stage'.$stage->id);
                }

                echo '<p>'.$stage->description.'</p>';
                ?>
                <?php if($own){ ?>
                <div class="btn-group">
                    <a class="btn" href="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'editplantask','sid'=>$stage->id,'pid'=>$plan->id))); ?>"><?php echo JText::_('COM_GOALS_ADD_TASK')?></a>
                    <input type="button" class="btn" onclick="goalgoto('<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'editstage','id'=>$stage->id,'pid'=>$plan->id)));?>')" value="<?php echo JText::_('COM_GOAL_MIL_EDITBUTTON'); ?>" />
                    <input type="button" class="btn" onclick="if(confirm('<?php echo JText::_('COM_GOALS_DELETE_MIL_MESS'); ?>')){goalgoto('<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('task'=>'stage.delete','id'=>$stage->id)));?>')}" value="<?php echo JText::_('COM_GOALS_DELETE_MILISTONE'); ?>" />
                </div>
                <?php } ?>
                <?php
                echo '<p class="text-right"><span class="icon-clock"> </span> '.JHtml::_('date', $stage->duedate, JText::_('DATE_FORMAT_LC3')).'</p>';

                echo JHtml::_('bootstrap.startAccordion', $stage->id.'task-slide', array('active' => 'task'.$this->active['task']));

                if($stage->tasks_count['all']){
                    foreach($stage->tasks as $task){
                        $date='';
                        if($task->date == '0000-00-00 00:00:00'){
                            $date = '<strong><small>'.JText::_('COM_GOALS_PLEASE_FILL_IN_THE_DATE').'</small></strong>';
                        } else {
                            $date = ''.JHtml::_('date', $task->date, JText::_('DATE_FORMAT_LC3')).'<br/>';
                        }
                        $state = "";
                        if($task->status){
                            $state_btn = "<button type='submit' class='btn btn-warning change-state'>Uncomplete</button>";
                            $state ="<span class='icon-signup'> </span> ";
                            //else $state = "<span class='icon-checkbox-unchecked'> </span> ";
                        }else {
                            $state_btn = "<button type='submit'  class='btn btn-success change-state'>Complete</button>";
                            $state = "";
                        }
                        $return_url = GoalsHelperFE::getReturnURL(null, array('view'=>'plantask','id'=>$task->id));
                        echo JHtml::_('bootstrap.addSlide', $stage->id.'task-slide', '<div class="row-fluid"><div class="span9">'.$state.JText::_($task->title).'</div><div class="span3 text-right">'.$date.'</div></div>', 'task'.$task->id);

                        ?>
                        <div class="row-fluid">
                            <div class="span12">
                                <form class="adform" action="<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('task'=>'plantask.complete','id'=>$task->id))); ?>" method="POST">
                                <?php if($own){ ?>
                                    <div class="btn-group">
                                        <?php echo $state_btn; ?>
                                        <input type="button" class="btn" onclick="goalgoto('<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'editplantask','id'=>$task->id,'pid'=>$plan->id)));?>')" value="<?php echo JText::_('COM_GOAL_REC_EDITBUTTON'); ?>" />
                                        <input type="button" class="btn" onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_MIL_MESS'); ?>'))goalgoto('<?php echo JRoute::_(GoalsHelperRoute::buildLink(array('task'=>'plantask.delete','id'=>$task->id)));?>')" value="<?php echo JText::_('COM_GOALS_DELETE_RECORD'); ?>" />
                                    </div>

                                    <input type="hidden" name="return" value="<?php echo $return_url; ?>" />
                                <?php } ?>
                                </form>
                                <table class="table">
                                    <tbody>

                        <?php
                                    if (isset($task->value))
                                    {
                                        $sign = '';
                                        if($task->value > 0 && $task->result_mode == 1) $sign = "+";
                                        echo '<tr><td><strong>'.JText::_('COM_GOALS_REC_VALUE').'</strong></td><td>'.$sign.$task->value.' '.$plan->metric.'</td></tr>';
                                    }
                        ?>

                                    <tr>
                            <?php
                                        $glink = JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'plan','id'=>$plan->id)));
                                        echo '<td><strong>'.JText::_('COM_GOALS_REC_GOAL').'</strong></td><td>'.'<a href="'.$glink.'">'.$plan->title.'</a></td>';
                            ?>
                                    </tr>
                                    <tr>
                            <?php
                                        echo '<td><strong>'.JText::_('COM_GOALS_REC_DESCRIPTION').'</strong></td><td>'.$task->description.'</td>';
                            ?>
                                    </tr>
                        <?php if(sizeof($task->cfields)){
                            foreach ( $task->cfields as $cf )
                            {

                                if ($cf->inp_values)
                                {
                                    if ($cf->type!='pc') $value  = json_decode($cf->inp_values);
                                    else
                                    {
                                        $value  = $cf->inp_values;
                                        $value=str_replace('"','',$value);
                                        if (file_exists(JPATH_SITE.$value) && is_file(JPATH_SITE.$value))
                                        {
                                            $value=str_replace(DS,'/',$value);
                                            $value = '<img src="'.JURI::root().$value.'" alt="" />';
                                        }
                                    }
                                    if (is_array($value)) $value = implode(', ',$value);
                                    ?>
                                    <tr>
                                        <td class="gl_label"><strong><?php echo $cf->title;?>: </strong></td>
                                        <td><?php echo $value;?></td>
                                    </tr>
                                    <?php
                                }
                            }

                        } ?>
                        <?php
                        //echo 'SMT DEBUG: <pre>'; print_R($this->ufields); echo '</pre>';

                        if(sizeof($task->ufields)){
                            foreach ( $task->ufields as $cf )
                            {
                                // echo json_decode(str_replace('"','',$cf->inp_values),true).'<br />';
                                if ($cf->inp_values)
                                {

                                    if ($cf->type!='pc') $value  = json_decode($cf->inp_values, true);
                                    else
                                    {
                                        $value  = $cf->inp_values;

                                        $value=str_replace(DS,'\\',$value);
                                        $value=str_replace("\\","/",$value);
                                        //fix for image
                                        $value=str_replace('"','',$value);
                                        if (file_exists(JPATH_SITE.$value) && is_file(JPATH_SITE.$value))
                                        {
                                            $value=str_replace(DS,'/',$value);
                                            $value = '<img src="'.JURI::root().$value.'" alt="" />';
                                        }
                                    }
                                    if (is_array($value)) $value = implode(', ',$value);
                                    ?>
                                    <tr>
                                        <td class="gl_label"><strong><?php echo $cf->title;?>: </strong></td>
                                        <td><?php echo $value;?></td>
                                    </tr>
                                    <?php
                                }
                            }

                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                        echo JHtml::_('bootstrap.endSlide');
                    }
                }

                echo JHtml::_('bootstrap.endAccordion');

                if(!$this->accordion['stages']){
                    echo JHtml::_('bootstrap.endSlide');
                }

            }
        }
        if(!$this->accordion['stages']){
            echo JHtml::_('bootstrap.endAccordion');
        }

        if(!$this->accordion['plans']){
            echo JHtml::_('bootstrap.endSlide');
        }
    }
    if(!$this->accordion['plans']){
        echo JHtml::_('bootstrap.endAccordion');
    }
}else { ?>
    <h2><?php echo JText::_('COM_GOALS_RECORDS_NOT_FOUND');?></h2>
<?php } ?>