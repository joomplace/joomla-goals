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
JHTML::_('behavior.calendar');

$function	= JRequest::getCmd('function', 'jSelectHabit');

?>
<script type="text/javascript">
	function hab_submit()
	{
		var form = document.adminForm;
		var start_val = form.hab_start.value;
		var finish_val = form.hab_fin.value;
		var usr = <?php echo (int) JFactory::getUser()->id;?>;
		if (window.parent) window.parent.<?php echo $function;?>(start_val, finish_val, usr);
		return null;
	}
</script>
<table class="admin" width="100%">
	<tbody>
		<tr>
			<td valign="top" width="100%" >
				<form action="<?php echo JRoute::_('index.php?option=com_goals&view=habits'); ?>" method="post" name="adminForm" id="adminForm" >
					<table class="table" width="100%">
						<tbody>
							<tr class="row0">
							<td>Start date</td>
							<td>
								<?php echo JHTML::_('calendar',date('Y-m-d',strtotime(date('Y-m-d'))-31500000),'hab_start', 'hab_start', '%Y-%m-%d'); ?>
							</td>
							</tr>
							<tr class="row1">
							<td>Finish date</td>
							<td>
								<?php echo JHTML::_('calendar',date('Y-m-d'),'hab_fin', 'hab_fin', '%Y-%m-%d'); ?>
							</td>
							</tr>
							<tr class="row0">
								<td colspan="3">
									<input type="button" class="button" name="name" value="Insert habits image" onclick="hab_submit();" />
								</td>
							</tr>
						</tbody>
					</table>
					<div>
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="layout" value="modal" />
						<?php echo JHtml::_('form.token'); ?>
					</div>
				</form>
			</td>
		</tr>
	</tbody>
</table>