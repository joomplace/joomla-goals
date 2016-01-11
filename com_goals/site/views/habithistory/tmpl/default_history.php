<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$items = $this->logs;
$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
?>
<script type="text/javascript">
function gl_change_status(id)
{
	if (id)
		{
		var url="<?php echo JURI::root()?>/index.php?option=com_goals&task=habit.changestatus&tmpl=component";
		var dan="hid=" + id;
		var statusdiv =  $('l_'+id);
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
<table class="table table-condensed" border="0" width="100%">
	<tr>
		<th><?php echo JText::_('COM_GOALS_HABITS_LOGS_HEADER_DATE'); ?></th>
		<th><?php echo JText::_('COM_GOALS_NEW_HABIT'); ?></th>

		<th></th>
	</tr>
			 <?php if (sizeof($items)) {
				  	for ( $i = 0, $n = sizeof( $items ); $i < $n; $i++ )
				  	{
				  		$habit = $items[$i];
				  		//echo 'SMT DEBUG: <pre>'; print_R($habit); echo '</pre>';
			  ?>
			  <tr>
			  		<td width="30%">
			  			<?php if (isset($habit->date)) echo JHtml::_('date', $habit->date, JText::_('DATE_FORMAT_LC2'));?>
					</td>
			  		<td>
			  			<?php  if (isset($habit->title)) echo $habit->title; ?>
			  		</td>
				  	<td width="15%">
						<input type="button" class="btn btn-small" onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_HABIT_LOG_MESS'); ?>'))goalgoto('<?php echo JRoute::_('index.php?option=com_goals&task=habit.deletelog&id='.(int)$habit->id.$tmpl);?>')" value="<?php echo JText::_('COM_GOALS_DELETE_RECORD'); ?>" />
				  	</td>
			  </tr>
			<?php
				}
				} else {?>
				<tr><td colspan="4"><div class="gl_msntf"><?php echo JText::_('COM_GOALS_HABITS_LOGS_NOT_FOUND');?></div></td></tr>
			<?php }?>

</table>