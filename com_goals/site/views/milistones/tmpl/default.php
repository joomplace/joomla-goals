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
$spec_goal=JRequest::getInt('gid');
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
	 <?php GoalsHelperFE::showDashHeader('','active','',''); ?>
 <div class="gl_goals">
<?php
$items = $this->mils;
if ($spec_goal && isset($items[0]))
{
	$glink = JRoute::_('index.php?option=com_goals&view=goal&id='.$items[0]->gid.$tmpl);
     echo '<h2 class="text-center">'.JText::_('COM_GOALS_MIL_GOAL_FOR').' <a href="'.$glink.'">'.$items[0]->gtitle.'</a></h2>';
 }
 else{
     echo '<h2 class="text-center">'.JText::_('COM_GOALS_MILISTONES').'</h2>';
 }
?>
 		<?php echo $this->loadTemplate('mils'); ?>
         <form method="POST" action="<?php echo JRoute::_('index.php?option=com_goals&view=milistones&gid='.$items[0]->gid.$itemId) ?>" class="pagination">
             <?php echo $this->pagination->getPagesLinks(); ?>
             <?php echo $this->pagination->getLimitBox(); ?>
         </form>
 </div>

</div>
<div class="clr"></div>
</div><!-- end #goals-wrap -->