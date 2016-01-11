<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
$user = JFactory::getUser();
$uid = JRequest::getInt('u',0);
$idr = JRequest::getInt('id',0);
$usid = $user->id;
$owner=false;
 if ($usid==$uid || $uid==0) {$owner=true;}
?>
<div id="goals-wrap">
<script type="text/javascript">
	function goalgoto(url)
	{
		location.href=url;
	}

	function gl_clear_filter()
	{
		$('gl_cal_start').set('value','');
		$('gl_cal_end').set('value','');
	}
</script>
 <div class="gl_dashboard">
	<?php GoalsHelper::showHabitHeader('','active',''); ?>
 <div class="gl_goals">

<?php
 	if ($owner && ($user->authorise('core.create', 'com_goals'))){
 ?>
 		<div><a href="<?php echo JRoute::_('index.php?option=com_goals&view=addhabitlog'.$tmpl);?>" class="btn" >New action</a></div>
 <form id="adminForm" class="" action="<?php echo JRoute::_('index.php?option=com_goals&view=habithistory'.$tmpl)?>" method="post">
	<fieldset class="filters form-inline well">
		<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
		<div class="filter-search">
            <label for="gl_cal_start" class="control-label"><?php echo JText::_('COM_GOALS_CALENDAR_START'); ?></label>
            <?php  echo JHTML::_('calendar', $this->state->get('filter.cal_start'), $name='cal_start', $id='gl_cal_start', $format = '%Y-%m-%d', $attribs = array('class'=>'input-small')); ?>
            <label for="gl_cal_end" class="control-label"><?php echo JText::_('COM_GOALS_CALENDAR_FINISH'); ?></label>
            <?php  echo JHTML::_('calendar', $this->state->get('filter.cal_end'), $name='cal_end', $id='gl_cal_end', $format = '%Y-%m-%d', $attribs = array('class'=>'input-small')); ?>
			<button type="submit" class="btn btn-small"><?php echo JText::_('JGLOBAL_FILTER_BUTTON'); ?></button>
			<button type="submit" class="btn btn-small" onclick="gl_clear_filter();"><?php echo JText::_('COM_GOALS_CLEAR_BUTTON'); ?></button>
		</div>
		<input type="hidden" name="view" value="habithistory" />
		<input type="hidden" name="option" value="com_goals" />
		<input type="hidden" name="limitstart" value="0" />
		 <?php if ($tmpl=='component') echo'<input type="hidden" name="tmpl" value="component" />';?>
	</fieldset>
</form>
<?php } ?>
		 <div align="center">
		 <?php
		 if (!$idr)
		 {
		 	if ($uid) $u = '&u='.$uid; else $u='';
		 ?>
			<img src="<?php echo JRoute::_('index.php?option=com_goals&task=habit.showallGraph&tmpl=component&filter=raw&stc='.$this->state->get('filter.cal_start').'&ecl='.$this->state->get('filter.cal_end').$u);?>" />
		<?php } ?>
		 </div>
 		<?php
 		if ($owner)
 			{	echo $this->loadTemplate('history');
 		?>
 		<div class="pagination gl_pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<?php } ?>
 </div>

</div>


<div class="clr"></div>
</div><!-- end #goals-wrap -->