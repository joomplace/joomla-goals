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
//if ($tmpl == 'component') $tmpl = '&tmpl=component';
//else $tmpl = '';
$tmpl = '';
$own = false;
if ($this->plan->uid == JFactory::getUser()->id) {
    $own = true;
}

$date_format = str_replace('%', '', $this->settings->chart_date_format);

?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm"
      id="adminForm">
    <div class="gl_dashboard">
        <?php 
			if($this->plan->percent<100) GoalsHelper::showDashHeader('','','active','');
			else GoalsHelper::showDashHeader('','','','active');
		?>
        <div class="gl_goals goals-item">
            <?php
            $item = $this->plan;
            $status_style = $item->status_image;
            $percent = $item->percent;

            ?>


            <div class="gl_goals-item row-fluid">
                <div class="span12">
                    <h3 class="text-center">
                        <?php
                        if (isset($item->deadline))
                            if ($item->deadline != '0000-00-00 00:00:00') {
                                echo '<span title="'.JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC3')).'" class="icon icon-clock '.$status_style.'"> </span>';
                            }
                        ?>
                        <?php echo $item->title ?>
                    </h3>
                    <?php if(isset($item->startup)){ ?><small class="text-center"><?php echo JText::_('COM_GOALS_STRATON') . ': ' . JHtml::_('date', $item->startup, JText::_('DATE_FORMAT_LC3')); ?></small><?php } ?>
                    <small class="text-center"><?php echo JText::_('COM_GOALS_DUE') . ': ' . JHtml::_('date', $item->deadline, JText::_('DATE_FORMAT_LC3')); ?></small>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="text-center">
                        <div class="well" style="margin: 10px 0px;">
                            <div class="gl_goal_progress">
                                <div class="goal-item-progress">
                                    <div class="progress progress-small progress-striped" style="height: 10px!important;margin: 0px;">
                                        <div class="bar"
                                             style="width:<?php echo $percent; ?>%; background-color: <?php echo $item->status ?>"></div>
                                    </div>
                                    <div class="progressbar_label_right"><?php echo $percent;?>%</div>
                                </div>
                            </div>
                            <div class="clr"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gl_goal_details">
                <div class="row-fluid">
                    <div class="span9">
                        <table class="table table-striped">
                            <?php if ($item->image && $item->image!='components/com_goals/assets/images/noimage.png' && is_file(JPATH_SITE . '/' . $item->image)) { ?>
                                <tr>
                                    <td class="text-center" colspan="2"><img src="<?php echo JURI::root().$item->image;?>" alt=""/></td>
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
                            <ul class="nav nav-tabs nav-stacked">
                                <li>
                                    <a class="state-link"
                                       href="<?php echo JRoute::_('index.php?option=com_goals&view=plantasks&pid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_PLANTASKS'); ?></a>
                                </li>
                                <li>
                                    <a class="state-link"
                                       href="<?php echo JRoute::_('index.php?option=com_goals&view=editplantask&pid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_ADD_PLANTASK')?></a>

                                </li>
                                <li>
                                    <a class="state-link"
                                       href="<?php echo JRoute::_('index.php?option=com_goals&view=editstage&pid=' . (int)$item->id . $tmpl);?>"><?php echo JText::_('COM_GOALS_ADD_STAGE')?></a>
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
                            </ul>
                        <?php } ?>
                    </div>
                </div>
                <?php if ($own) { ?>
                    <div class="goal-item-actions text-right">
                        <div class="goals-edit-item">
                            <button class="btn"
                                    onclick="location.href='<?php echo JRoute::_('index.php?option=com_goals&view=editplan&id=' . (int)$item->id . $tmpl);?>';return false;"><?php echo JText::_('COM_GOAL_PLAN_EDITBUTTON'); ?></button>
                            <button class="btn"
                                    onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_GOAL_MESS'); ?>')) location.href='<?php echo JRoute::_('index.php?option=com_goals&task=plan.delete&id=' . (int)$item->id . $tmpl);?>'; return false;"><?php echo JText::_('COM_GOALS_DELETE_PLAN'); ?></button>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <input type="hidden" name="task" value=""/>
</form>