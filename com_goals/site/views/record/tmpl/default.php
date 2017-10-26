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
	function goalgoto(url)
	{
		location.href=url;
	}
</script>
 <div class="gl_dashboard">
	<?php GoalsHelperFE::showDashHeader('','active','',''); ?>
 <div class="gl_goals">
<?php
$item = $this->rec;
if (JRequest::getInt('gid') && isset($item))
{
	$glink = JRoute::_('index.php?option=com_goals&view=goal&id='.$item->gid.$tmpl);
	echo '<h2>'.JText::_('COM_GOALS_MIL_GOAL').':<a href="'.$glink.'">'.$item->gtitle.'</a></h2>';
}
?>
 		<h2><?php echo JText::_('COM_GOALS_RECORD'); ?></h2>
 		<?php echo $this->loadTemplate('rec'); ?>
 </div>

</div>
<div class="clr"></div>

</div><!-- end #goals-wrap -->