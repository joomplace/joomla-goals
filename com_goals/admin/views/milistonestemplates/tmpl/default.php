<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$search 	= $this->escape($this->state->get('filter.search'));
?>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_goals&view=milistonestemplates'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_GOALS_FILTER_SEARCH_DESC'); ?></label>
				<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_GOALS_FILTER_SEARCH_DESC'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_GOALS_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>
		<div class="clearfix"> </div>
				<table class="table table-striped">
						<thead>
							<tr>
								<th width="1%">
									<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
								</th>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
								</th>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'COM_GOALS_TASK_DUEDATE', 'duedate', $listDirn, $listOrder); ?>							
								</th>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'COM_GOALS_GOALTEMPLATE_SELECT', 'gtitle', $listDirn, $listOrder); ?>
								</th>	
								<th>
									<?php echo JHtml::_('grid.sort', 'COM_GOALS_TASK_STATUS', 'status', $listDirn, $listOrder); ?>							
								</th>							
								<?php /*
								<th>
									<?php echo JText::_('Color'); ?>
								</th>
								*/ 
								?>
								<th width="1%" class="nowrap">
									<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="9"><?php if ($this->pagination) echo $this->pagination->getListFooter(); ?></td>
							</tr>
						</tfoot>
						<tbody>
						<?php if (sizeof($this->milistones)) {foreach($this->milistones as $i => $item) {
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="center">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								<td>
								<?php if ($this->canDo->get('core.edit') or ($this->canDo->get('core.edit.own') and $item->user_id == $this->user->get('id'))) { ?>
									<a href="<?php echo JRoute::_('index.php?option=com_goals&task=milistonestemplate.edit&id='.$item->id);?>">
										<?php echo $this->escape($item->title); ?>
									</a>
								<?php } else { ?>
									<?php echo $this->escape($item->title); ?>
								<?php } ?>
								</td>
								<td>
									<?php echo $item->daysto; ?>
								</td>
								<td>
									<img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/goals.png" alt="" />
									<?php echo $item->gtitle; ?>
								</td>
								<td align="center">
									<?php echo $item->status?JText::_('COM_GOALS_COMPLETE'):JText::_('COM_GOALS_NOT_COMPLETE'); ?>
								</td>
								<?php
								?>					
								<td>
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php }} else { ?>
							<tr>
								<td colspan="9" align="center" >
									<?php echo JText::sprintf('COM_GOALS_FIELD_NONE', 'milistones'); ?>
									<a href="<?php echo JRoute::_('index.php?option=com_goals&task=milistonestemplate.add'); ?>" >
										<?php echo JText::_('COM_GOALS_FIELD_NONE_A'); ?>
									</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<div>
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="0" />
						<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
						<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</div>
				</form>
