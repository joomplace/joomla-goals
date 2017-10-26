<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
$tmpl = JRequest::getVar('tmpl');
//if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';
$tmpl = '';
$return = $this->return;
$own = false;
if ($this->goal->uid == JFactory::getUser()->id) {
    $own = true;
}

$date_format = str_replace('%', '', $this->settings->chart_date_format);

JFactory::getDocument()->addScriptDeclaration('
jQuery(document).ready(function($) {
       $("#chart_field").change(function() { 
			var fid = $("#chart_field").val();
			$("#goal_chart").attr(\'src\', "'.JRoute::_('index.php?option=com_goals&task=goal.showGoalGraph&tmpl=component&filter=raw&id=' . (int)$this->goal->id).'&fid="+fid); 
        });
    })  
');

?>
    <div class="gl_dashboard">
        <?php
			if(!$this->goal->is_complete) GoalsHelper::showDashHeader('','active','','');
			else GoalsHelper::showDashHeader('','','','active');
		?>
        <div class="gl_goals goals-item">
            <?php
            $item = $this->goal;
            $status_style = $item->status_image;
            $percent = $item->percent;

            ?>


            <div class="gl_goals-item row-fluid">
                <div class="">
                    <h3 class="text-center">
                        <?php echo $item->title ?>
                    </h3>
                    <div class="gl_goal_progress">
                        <div class="goal-item-progress">
                            <div class="progress progress-small progress-striped">
                                <div class="bar"
                                     style="width:<?php echo $percent?>%; background-color: <?php echo $item->status ?>"></div>
                            </div>
                            <div class="progressbar_label_right"><?php echo $percent;?>%</div>
                        </div>
                    </div>
                    <div class="clr"></div>
                </div>
                <div class="">
                    <div class="row-fluid">
                        <div class="span4">
                            <?php if(!isset($item->startup) || $item->startup == '0000-00-00 00:00:00'){ ?>
                                <small class="text-left">
                                    <?php
                                    if (isset($item->deadline))
                                        if ($item->deadline != '0000-00-00 00:00:00') {
                                            echo '<span title="'.JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC3')).'" class="icon icon-clock '.$status_style.'"> </span>';
                                        }
                                    ?>
                                    <?php echo JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC3')); ?>
                                </small>
                            <?php }else{ ?>
                                <small class="text-left">
                                    <?php
                                    if (isset($item->deadline))
                                        if ($item->deadline != '0000-00-00 00:00:00' && $item->startup != '0000-00-00 00:00:00') {
                                            echo '<span title="'.JText::_('COM_GOALS_TIMERANGE') . ': ' . JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC3')).'" class="icon icon-clock '.$status_style.'"> </span>';
                                            echo JHtml::_('date', $item->startup, JText::_('DATE_FORMAT_LC3')).' - '.JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC3'));
                                        }
                                    ?>
                                </small>
                            <?php } ?>
                        </div>
                        <div class="span8 text-right">
                            <?php if ($own) { ?>
                                <form class="row-fluid" action="<?php echo JRoute::_('index.php?option=com_goals&view=goal&task=record.save'); ?>" id="quick-record" method="post" >
                                    <div class="input-append">
                                        <input name="jform[value]" class="span3" value="1" type="number">
                                        <input name="jform[title]" value="<?php echo JText::_('COM_GOALS_NEW_QUICK_RECORD');?>" type="hidden">
                                        <input name="jform[gid]" value="<?php echo $item->id; ?>" type="hidden">
                                        <input name="jform[result_mode]" value="1" type="hidden">
                                        <button class="btn" type="submit"><?php echo JText::_('COM_GOALS_RECORDIT')?></button>
                                        <input name="return" value="<?php echo $return; ?>" type="hidden">
                                        <?php echo JHtml::_('form.token'); ?>
                                    </div>
                                </form>
                                <?php JFactory::getDocument()->addScriptDeclaration('
                                    jQuery("document").ready(function($) {
                                        $("#quick-record").submit(function( event ) {
                                            $("#quick-record .btn").prepend(\'<img style="height: 15px;padding: 0px 5px 0px 0px;" src="'.JUri::base().'administrator/components/com_goals/assets/img/loading.gif" />\');
                                        });
                                    });
                                ') ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            echo JHtml::_('bootstrap.startAccordion', 'moreinfo', array('active' => 'act'));
            ?>
            <?php if ($own) {
                echo JHtml::_('bootstrap.addSlide', 'moreinfo', JText::_('COM_GOALS_GRAPH_ACT'), 'act');
            }else{
                echo JHtml::_('bootstrap.addSlide', 'moreinfo', JText::_('COM_GOALS_GRAPH'), 'act');
            } ?>
            <div class="row-fluid">
                <div class="span12">
                    <div class="text-center">
                        <?php //if ($item->records_count) {?>
                        <img id="goal_chart" src="<?php echo JRoute::_('index.php?option=com_goals&task=goal.showGoalGraph&tmpl=component&filter=raw&id=' . (int)$item->id)?>"/>
                        <?php //} ?>
                    </div>
                    <div class="text-center">
                        <select name="chart_field" id="chart_field">
                            <option value="0"><?php echo JText::_('COM_GOALS_TASK_VALUE'); ?></option>
                            <?php if($item->fields) foreach($item->fields as $chart_fields){ ?>
                                <option value="<?php echo $chart_fields->id; ?>"><?php echo $chart_fields->title; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php if ($own) { ?>
            <div class="row-fluid">
                <div class="span12">
                    <ul class="nav nav-pills nav-justified">
                        <li>
                            <a class="state-link"
                               href="<?php echo JRoute::_('index.php?option=com_goals&view=records&gid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_SHOW_ALL_TASKS'); ?>
                                (<?php echo (int)$item->task_count;?>)</a>
                        </li>
                        <li>
                            <a class="state-link"
                               href="<?php echo JRoute::_('index.php?option=com_goals&view=milistones&gid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_SHOW_ALL_MILISTONES'); ?>
                                (<?php echo (int)$item->milistones_count;?>)</a>
                        </li>
                        <li>
                            <a class="state-link"
                               href="<?php echo JRoute::_('index.php?option=com_goals&view=editrecord&gid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_ADD_RECORD')?></a>
                        </li>
                        <li>
                            <a class="state-link"
                               href="<?php echo JRoute::_('index.php?option=com_goals&view=editmilistone&gid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_ADD_MILESTONE')?></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="state-link" onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editgoal&id='.(int)$item->id.$tmpl);?>';return false;"><?php echo JText::_('COM_GOAL_GOAL_EDITBUTTON'); ?></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="state-link"
                                    onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_GOAL_MESS'); ?>')) location.href='<?php echo JRoute::_('index.php?option=com_goals&task=goal.delete&id=' . (int)$item->id . $tmpl);?>'; return false;"><?php echo JText::_('COM_GOALS_DELETE_GOAL'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php } ?>
            <?php
                echo JHtml::_('bootstrap.endSlide');
                echo JHtml::_('bootstrap.addSlide', 'moreinfo', JText::_('COM_GOALS_MORE_GOAL_DETAILS'), 'info');
            ?>
            <table class="table table-striped">
                <?php if ($item->image && $item->image!='components/com_goals/assets/images/noimage.png' && is_file(JPATH_SITE . '/' . $item->image)) { ?>
                    <tr>
                        <td style="text-align: center;" colspan="2"><img src="<?php echo JURI::root().$item->image;?>" alt=""/></td>
                    </tr>
                <?php }?>
                <tr>
                    <td><?php echo JText::_('COM_GOALS_GOAL_DESCRIPTION');?>:</td>
                    <td><?php echo $item->description;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('COM_GOALS_GOAL_CATEGORY');?>:</td>
                    <td><?php echo $item->catname;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('COM_GOALS_GOAL_START');?>:</td>
                    <td><?php echo $item->start;?> <?php echo $item->metric;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('COM_GOALS_GOAL_FINISH');?>:</td>
                    <td><?php echo $item->finish;?> <?php echo $item->metric;?></td>
                </tr>
            </table>
            <?php
            echo JHtml::_('bootstrap.endSlide');
            if ($own) {
                if (isset($item->milistones)) {
                    echo JHtml::_('bootstrap.addSlide', 'moreinfo', JText::_('COM_GOALS_MILESTONE'), 'mile');
                    if(count($item->milistones)){
                    ?>
                    <div class="row-fluid">
                        <div class="span12">
                                <div class="gl_milistones">
                                    <?php
                                        foreach($item->milistones as $m){
                                            ?>
                                            <h4>
                                                <?php echo $m->title; ?>
                                            </h4>
                                            <?php if(!$m->status){ ?>
                                            <div>
                                                <div class="row-fluid">
                                                    <div class="span3">
                                                        <strong>
                                                            <span class="icon-flag"> </span> <?php echo JText::_('COM_GOALS_MILESTONE_NEEDED_VALUE'); ?>
                                                        </strong>
                                                    </div>
                                                    <div class="span9">
                                                        <?php echo ( $m->value - $item->summary ).' '.$item->metric; ?>
                                                    </div>
                                                </div>
                                                <div class="row-fluid">
                                                    <div class="span3">
                                                        <strong>
                                                        <span class="icon-clock"> </span>
                                                            <?php echo JText::_('COM_GOALS_DUE'); ?>
                                                        </strong>
                                                    </div>
                                                    <div class="span9">
                                                        <?php echo  JHtml::_('date', $m->duedate, JText::_('DATE_FORMAT_LC3')); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php }else{ ?>
                                                <div>
                                                    <span class="icon-checkmark"> </span> <?php echo $m->description; ?>
                                                </div>
                                            <?php } ?>
                                            <?php
                                        }
                                    ?>
                                </div>
                        </div>
                    </div>
                    <?php
                    }
                    else{
                        echo JText::_('COM_GOALS_MILISTONES_NOT_FOUND');
                    }
                    echo JHtml::_('bootstrap.endSlide');
                }
            }
            echo JHtml::_('bootstrap.endAccordion');
            ?>

        </div>
    </div>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm"
      id="adminForm">

    <input type="hidden" name="task" value=""/>
</form>

<?php /* ?>
<hr/>
<hr/>
<hr/>
<hr/>




<div class="gl_dashboard">
    <?php
    if(!$this->goal->is_complete) GoalsHelper::showDashHeader('','active','','');
    else GoalsHelper::showDashHeader('','','','active');
    ?>
    <div class="gl_goals goals-item">
        <?php
        $item = $this->goal;
        $status_style = $item->status_image;
        $percent = $item->percent;

        ?>


        <div class="gl_goals-item row-fluid">
            <div class="span9">
                <h3 class="text-center">
                    <?php
                    if (isset($item->deadline))
                        if ($item->deadline != '0000-00-00 00:00:00') {
                            echo '<span title="'.JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC3')).'" class="icon icon-clock '.$status_style.'"> </span>';
                        }
                    ?>
                    <?php echo $item->title ?>
                </h3>
                <small class="text-center"><?php echo JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC3')); ?></small>
            </div>
            <div class="span3">
                <div class="well">
                    <div class="gl_goal_progress">
                        <div class="goal-item-progress">
                            <div class="progress progress-small progress-striped">
                                <div class="bar"
                                     style="width:<?php echo $percent?>%; background-color: <?php echo $item->status ?>"></div>
                            </div>
                            <div class="progressbar_label_right"><?php echo $percent;?>%</div>
                        </div>
                    </div>
                    <div class="clr"></div>
                </div>
            </div>
        </div>
        <div class="gl_goal_details">
            <div class="row-fluid">
                <div class="span9">
                    <table class="table table-striped">
                        <?php if ($item->image && $item->image!='components/com_goals/assets/images/noimage.png' && is_file(JPATH_SITE . '/' . $item->image)) { ?>
                            <tr>
                                <td colspan="2"><img src="<?php echo JURI::root().$item->image;?>" alt=""/></td>
                            </tr>
                        <?php }?>
                        <tr>
                            <td><?php echo JText::_('COM_GOALS_GOAL_DESCRIPTION');?>:</td>
                            <td><?php echo $item->description;?></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_('COM_GOALS_GOAL_CATEGORY');?>:</td>
                            <td><?php echo $item->catname;?></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_('COM_GOALS_GOAL_START');?>:</td>
                            <td><?php echo $item->start;?> <?php echo $item->metric;?></td>
                        </tr>
                        <tr>
                            <td><?php echo JText::_('COM_GOALS_GOAL_FINISH');?>:</td>
                            <td><?php echo $item->finish;?> <?php echo $item->metric;?></td>
                        </tr>
                    </table>
                </div>

                <div class="span3">
                    <?php if ($own) { ?>
                        <form class="row-fluid" action="<?php echo JRoute::_('index.php?option=com_goals&view=goal&task=record.save'); ?>" method="post" >
                            <div class="input-prepend">
                                <button class="btn span7" type="submit"><?php echo JText::_('COM_GOALS_RECORDIT')?></button>
                                <input name="jform[value]" class="span5" value="1" type="number">
                                <input name="jform[title]" value="<?php echo JText::_('COM_GOALS_NEW_QUICK_RECORD').' '.JHtml::_('date', 'now', JText::_('DATE_FORMAT_LC3'));?>" type="hidden">
                                <input name="jform[gid]" value="<?php echo $item->id; ?>" type="hidden">
                                <input name="jform[result_mode]" value="1" type="hidden">
                                <input name="return" value="<?php echo $return; ?>" type="hidden">
                                <?php echo JHtml::_('form.token'); ?>
                            </div>
                        </form>

                        <ul class="nav nav-tabs nav-stacked">
                            <li>
                                <a class="state-link"
                                   href="<?php echo JRoute::_('index.php?option=com_goals&view=records&gid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_SHOW_ALL_TASKS'); ?>
                                    (<?php echo (int)$item->task_count;?>)</a>
                            </li>
                            <li>
                                <a class="state-link"
                                   href="<?php echo JRoute::_('index.php?option=com_goals&view=milistones&gid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_SHOW_ALL_MILISTONES'); ?>
                                    (<?php echo (int)$item->milistones_count;?>)</a>
                            </li>
                            <li>
                                <a class="state-link"
                                   href="<?php echo JRoute::_('index.php?option=com_goals&view=editrecord&gid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_ADD_RECORD')?></a>
                            </li>
                            <li>
                                <a class="state-link"
                                   href="<?php echo JRoute::_('index.php?option=com_goals&view=editmilistone&gid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_ADD_MILESTONE')?></a>
                            </li>
                            <?php /*
                            <li>
                                <a href="javascript:void(0)" class="state-link" onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editgoal&id='.(int)$item->id.$tmpl);?>';return false;"><?php echo JText::_('COM_GOAL_GOAL_EDITBUTTON'); ?></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="state-link"
                                        onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_GOAL_MESS'); ?>')) location.href='<?php echo JRoute::_('index.php?option=com_goals&task=goal.delete&id=' . (int)$item->id . $tmpl);?>'; return false;"><?php echo JText::_('COM_GOALS_DELETE_GOAL'); ?></a>
                            </li>
 */ ?>
<?php /* ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>
            <?php if ($own) { ?>
                <div class="goal-item-actions text-right">
                    <div class="goals-edit-item">
                        <button class="btn" onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editgoal&id='.(int)$item->id.$tmpl);?>';return false;"><?php echo JText::_('COM_GOAL_GOAL_EDITBUTTON'); ?></button>
                        <button class="btn"
                                onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_GOAL_MESS'); ?>')) location.href='<?php echo JRoute::_('index.php?option=com_goals&task=goal.delete&id=' . (int)$item->id . $tmpl);?>'; return false;"><?php echo JText::_('COM_GOALS_DELETE_GOAL'); ?></button>
                    </div>
                </div>
            <?php } ?>

        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="text-center">
                    <select name="chart_field" id="chart_field">
                        <option value="0"><?php echo JText::_('COM_GOALS_TASK_VALUE'); ?></option>
                        <?php if($item->fields) foreach($item->fields as $chart_fields){ ?>
                            <option value="<?php echo $chart_fields->id; ?>"><?php echo $chart_fields->title; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="text-center">
                    <?php //if ($item->records_count) {?>
                    <img id="goal_chart" src="<?php echo JRoute::_('index.php?option=com_goals&task=goal.showGoalGraph&tmpl=component&filter=raw&id=' . (int)$item->id)?>"/>
                    <?php //} ?>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <?php if ($own) { ?>
                    <div class="gl_milistones">
                        <?php if (isset($item->milistones)) {
                            GoalsHelper::showMilistones($item->milistones);
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm"
      id="adminForm">

    <input type="hidden" name="task" value=""/>
</form>
<?php */ ?>