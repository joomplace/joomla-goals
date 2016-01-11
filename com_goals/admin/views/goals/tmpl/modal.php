<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$search 	= $this->escape($this->state->get('filter.search_goals'));
$function	= JRequest::getCmd('function', 'jSelectGoal');
?>
<table class="admin" width="100%">
	<tbody>
		<tr>
			<td valign="top" width="100%" >
				<form action="<?php echo JRoute::_('index.php?option=com_goals&view=goals'); ?>" method="post" name="adminForm" id="adminForm" >
					<?php if ($this->goals || $search) { ?>
					<fieldset id="filter-bar">
						<div class="filter-search fltlft">
							<label class="filter-search-lbl" for="filter_goals">
								<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
							</label>
							<input type="text" name="filter_goals" id="filter_goals" value="<?php echo $search; ?>" title="" />
							<button type="submit">
								<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
							</button>
							<button type="button" onclick="document.id('filter_goals').value='';this.form.submit();">
								<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
							</button>
						</div>					
					</fieldset>
					<?php } ?>
					<table class="table" width="100%">
						<thead>
							<tr>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
								</th>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'COM_GOALS_DEADLINE', 'deadline', $listDirn, $listOrder); ?>
								</th>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'COM_GOALS_TASK_CAT', 'ctitle', $listDirn, $listOrder); ?>
								</th>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'COM_GOALS_START', 'start', $listDirn, $listOrder); ?>
								</th>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'COM_GOALS_FINISH', 'finish', $listDirn, $listOrder); ?>
								</th>
								<th class="gl_left">
									<?php echo JHtml::_('grid.sort', 'COM_GOALS_METRIC', 'metric', $listDirn, $listOrder); ?>
								</th>
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
						<?php if (sizeof($this->goals)) {foreach($this->goals as $i => $item) {
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td>
								<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $item->id; ?>');">
									<?php echo $this->escape($item->title); ?>
								</a>
								</td>
								<td>									
									<?php echo JHtml::_('date',$item->deadline); ?>
								</td>
								<td>
									<img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/categories.png" alt="" />
									<?php echo $item->ctitle; ?>
								</td>								
								<td>
									<?php echo $item->start; ?>
								</td>
								<td>
									<?php echo $item->finish; ?>
								</td>
								<td>
									<?php echo $item->metric; ?>
								</td>					
								<td>
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php }} else { ?>
							<tr>
								<td colspan="9" align="center" >
									<?php echo JText::sprintf('COM_GOALS_FIELD_NONE', 'goals'); ?>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<div>
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="0" />
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="layout" value="modal" />
						<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
						<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</div>
				</form>
			</td>
		</tr>
	</tbody>
</table>