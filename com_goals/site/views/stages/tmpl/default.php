<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
//$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
$tmpl='';
?>
<div id="goals-wrap">
<script type="text/javascript">
window.addEvent('domready',function() {
 	var myAccordion = new Fx.Accordion($$(".gl_goal_togglers"),$$(".gl_goal_details"));
});
</script>
<script type="text/javascript">
	function goalgoto(url)
	{
		location.href=url;
	}
</script>
 <div class="gl_dashboard">
	 <?php GoalsHelperFE::showDashHeader('','','active',''); ?>
 <div class="gl_goals">
<?php
$items = $this->stages;
if (JRequest::getInt('pid') && isset($items[0]))
{
	$glink = JRoute::_('index.php?option=com_goals&view=plan&id='.$items[0]->pid.$tmpl);
	echo '<h2>'.JText::_('COM_GOALS_STAGE_PLAN').': <a href="'.$glink.'">'.$items[0]->gtitle.'</a></h2>';
}
?>
 		<h2><?php echo JText::_('COM_GOALS_STAGES'); ?>:</h2>
 		<?php echo $this->loadTemplate('stages'); ?>
 		<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
 </div>

</div>
<div class="clr"></div>

</div><!-- end #goals-wrap -->