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
if ($this->accordion['plans'])
{
    foreach($this->plans as $pl){
        $glink = JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'plan','id'=>$pl->id)));
        echo '<h2 class="text-center">'.JText::_('COM_GOALS_STAGES_AND_TASKS_FOR_PLAN').' <a href="'.$glink.'">'.$pl->title.'</a></h2>';
    }
}else{
    echo '<h2 class="text-center">'.JText::_('COM_GOALS_STAGES_AND_TASKS').'</h2>';
}
?>
 		<?php echo $this->loadTemplate('plantasks'); ?>
 </div>

</div>
<div class="clr"></div>

</div><!-- end #goals-wrap -->