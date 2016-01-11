<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.multiselect');
//JHtml::_('formbehavior.chosen', 'select');
$multiple_select = false;

$return = $this->return;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'g.deadline';
$sortFields = $this->getSortFields();
$assoc		= JLanguageAssociations::isEnabled();
$search = $this->escape($this->state->get('filter.search'));
$category = $this->escape($this->state->get('filter.category_id'));
if($search || $category){
    $filtershead = JHtml::_('bootstrap.startAccordion', 'filters', array('active' => 'fil'));
    $filtabhead = JHtml::_('bootstrap.startTabSet', 'tools', array('active' => 'filt'));
}else{
    $filtershead = JHtml::_('bootstrap.startAccordion', 'filters', array('active' => ''));
    $filtabhead = JHtml::_('bootstrap.startTabSet', 'tools', array('active' => 'actions'));
}
?>

<script type="text/javascript">
    Joomla.orderTable = function()
    {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>')
        {
            dirn = 'asc';
        }
        else
        {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<div id="goals-wrap" class="row-fluid">
    <div class="gl_dashboard span12">
        <?php GoalsHelper::showDashHeader('','active','',''); ?>

        <div class="well">

            <?php
            echo $filtershead;
            ?>
            <?php
            echo JHtml::_('bootstrap.addSlide', 'filters', '<div>'.JText::_('COM_GOALS_GOALS_TOOLS').'</div>', 'fil');
            ?>
            <?php echo $filtabhead; ?>
                <?php echo JHtml::_('bootstrap.addTab', 'tools', 'actions', JText::_('Fast management')); ?>
                    <?php// if ($own) { ?>
                    <form class="" action="<?php echo JRoute::_('index.php?option=com_goals&view=goal&task=record.save'); ?>" id="quick-record" method="post" >
                        <div class="row-fluid">
                                <div class="input-append input-prepend">
                                    <input name="jform[value]" class="span2 fe" value="1" type="number">
                                    <span style="min-width: 35px;" class="add-on fe"></span>
                                    <select name="jform[gid]" id="jform_gid" class="span5 fe">
                                        <option data-metric="" value=""><?php echo JText::_('COM_GOALS_CHOOSE_GOAL_FOR_QUICK_RECORD'); ?></option>
                                        <?php echo $this->getGoalsOpt(); ?>
                                    </select>
                                    <button class="btn fe" type="submit"><?php echo JText::_('COM_GOALS_RECORDIT')?></button>
                                </div>
                                <input name="jform[title]" value="<?php echo JText::_('COM_GOALS_NEW_QUICK_RECORD');?>" type="hidden">
                                <input name="jform[result_mode]" value="1" type="hidden">
                                <input name="return" value="<?php echo $return; ?>" type="hidden">
                                <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </form>
                    <?php JFactory::getDocument()->addScriptDeclaration('
                                        jQuery("document").ready(function($) {
                                            $("#quick-record #jform_gid").on("change", function() {
                                              $("#quick-record .add-on").html($("#quick-record #jform_gid option:selected").data("metric"));
                                            });
                                            $("#quick-record").submit(function( event ) {
                                                 if(!$("#quick-record #jform_gid").val()){
                                                    alert( "'.JText::_('COM_GOALS_QUICK_REC_PLEASE_CHOOSE').'" );
                                                    event.preventDefault();
                                                 }else{
                                                    $("#quick-record .btn").prepend(\'<img style="height: 15px;padding: 0px 5px 0px 0px;" src="'.JUri::base().'administrator/components/com_goals/assets/img/loading.gif" />\');
                                                 }
                                            });
                                        });
                                    ') ?>
                    <?php// } ?>
                <?php echo JHtml::_('bootstrap.endTab');?>
                <?php echo JHtml::_('bootstrap.addTab', 'tools', 'filt', JText::_('COM_GOALS_GOALS_SEARCH_FILTERS')); ?>
            <form class="" action="<?php echo JRoute::_('index.php?option=com_goals&view=goals&Itemid='.GoalsHelperRoute::getClosesItemId(array('view' => 'goals'))) ?>" method="post" name="adForm" id="adminForm">
                    <div class="row-fluid">
                        <div id="filter-bar" class="btn-toolbar span6" style="margin: 0px;">
                            <div class="filter-search btn-group pull-left">
                                <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_GOALS_SEARCH_BY_TITLE_TIP'); ?></label>
                                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_GOALS_SEARCH_BY_TITLE'); ?>" value="<?php echo $search; ?>" class="hasTooltip" title="" data-original-title="<?php echo JText::_('COM_GOALS_SEARCH_BY_TITLE_TIP'); ?>">
                            </div>
                            <div class="btn-group pull-left">
                                <button type="submit" class="btn hasTooltip" title="" data-original-title="<?php echo JText::_('COM_GOALS_SEARCH'); ?>"><i class="icon-search"></i></button>
                                <button type="button" class="btn hasTooltip" title="" onclick="document.id('filter_search').value='';this.form.submit();" data-original-title="<?php echo JText::_('COM_GOALS_CLEAR'); ?>"><i class="icon-remove"></i></button>
                            </div>
                        </div>
                        <?php foreach($this->sidebar as $filter){ ?>
                            <div class="filter-select span6">
                                <label for="<?php echo $filter['name']; ?>" class="element-invisible"><?php echo $filter['label']; ?></label>
                                <select <?php if($multiple_select) echo 'multiple="multiple"'; ?> name="<?php echo $filter['name']; ?><?php if($multiple_select) echo '[]'; ?>" id="<?php echo $filter['name']; ?>" class="span12" onchange="this.form.submit()">
                                    <option value=""><?php echo $filter['label']; ?></option>
                                    <?php echo $filter['options']; ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                <input type="hidden" name="task" value="">
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
            </form>
                <?php echo JHtml::_('bootstrap.endTab');?>
            <?php echo JHtml::_('bootstrap.endTabSet'); ?>


            <?php

            echo JHtml::_('bootstrap.endSlide');
            echo JHtml::_('bootstrap.endAccordion');
            ?>
        </div>




        <form class="" action="<?php echo JRoute::_('index.php?option=com_goals&view=goals&Itemid='.GoalsHelperRoute::getClosesItemId(array('view' => 'goals'))) ?>" method="post" name="adForm" id="adminForm">



            <div class="row-fluid">
                <div id="j-main-container" class="span6">
                    <ul class="nav nav-pills">
                        <li><span><?php echo JText::_('COM_GOALS_CLICK_TO_ORDERBY'); ?></span></li>
                        <li><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'g.id', $listDirn, $listOrder); ?></li>
                        <li><?php echo JHtml::_('grid.sort', 'COM_GOALS_ORDER_DUE', 'g.deadline', $listDirn, $listOrder); ?></li>
                        <li><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'g.title', $listDirn, $listOrder); ?></li>
                    </ul>
                 </div>
            </div>

         <div class="gl_goals well">
            <?php include_once('default_goals.php'); ?>
            <div class="pagination row-fluid">
                <div class="span6">
                 <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
                <div class="span6 text-right">
                  <?php echo $this->pagination->getLimitBox(); ?>
                </div>
            </div>
         </div>
        </form>
    </div>
    <div class="clr"></div>
</div><!-- end #goals-wrap -->