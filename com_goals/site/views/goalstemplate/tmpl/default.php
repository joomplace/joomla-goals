<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$items = $this->items;
?>
<div id="goals-wrap">
    <?php GoalsHelperFE::showDashHeader('','active','',''); ?>

    <div class="goals-content">
        <h2><?php echo JText::_('COM_GOALS_SELECT_TEMPLATE_FOR_GOAL'); ?>:</h2>
		
            <?php foreach($this->items as $template) {
            if (!is_file(JPATH_SITE.'/'.$template->image)) $template->image='components/com_goals/assets/images/no_image.png';
            ?>
				<div class="row-fluid" style="padding-bottom: 20px">
					<div class="span2">
						 <a href="<?php echo JRoute::_('index.php?option=com_goals&view=editgoal&tempid='.$template->id); ?>"><img style="max-width:100%;" src="<?php echo $template->image; ?>" title="<?php echo $template->title; ?>"/></a>
					</div>
					<div class="span10">
						<h5>
							 <a href="<?php echo JRoute::_('index.php?option=com_goals&view=editgoal&tempid='.$template->id); ?>"><?php echo $template->title; ?></a>
						</h5>
						<?php if($template->description){ ?>
						<p>
							<?php echo $template->description; ?>
						</p>
						<?php } ?>
						<div class="goal-item-progress" style="max-width: 100px; margin: 0.2em 1em;">
							<div class="progressbar_label_left" style="position: absolute;left: -1.2em;  top: 0;"><?php echo $template->start.' '.$template->metric; ?></div>
							<div class="progress progress-small progress-striped">
							</div>
							<div class="progressbar_label_right"><?php echo $template->finish.' '.$template->metric; ?></div>
						</div>
					</div>
				</div>
            <?php } ?>
        <form class="" action="<?php echo JRoute::_('index.php?option=com_goals&view=goalstemplate&Itemid='.GoalsHelperRoute::getClosesItemId(array('view' => 'goals'))) ?>" method="post" name="adForm" id="adminForm">
            <div class="pagination row-fluid">
                <div class="span6">
                 <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
                <div class="span6 text-right">
                  <?php echo $this->pagination->getLimitBox(); ?>
                </div>
            </div>
        </form>
    </div>

</div><!-- end #goals-wrap -->