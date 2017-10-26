<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$items = $this->userfields;
$user = JFactory::getUser();
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
	<?php GoalsHelperFE::showDashHeader('','','active',''); ?>
 <div class="gl_goals">
 		<h2><?php echo JText::_('COM_GOALS_MY_FIELDS_PLANS'); ?>:</h2>
 		<?php
 	if ($user->authorise('core.create', 'com_goals')){
 		?>
 		<p class="goals-actions"><a href="<?php echo JRoute::_('index.php?option=com_goals&view=editplanuserfield'.$tmpl);?>" class="btn" ><?php echo JText::_('COM_GOALS_FIELDS_NEW_FIELD') ?></a></p>

 		<?php
 	}

 		 echo $this->loadTemplate('flds'); ?>
 		<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
 </div>

</div>
<div class="clr"></div>

</div><!-- end #goals-wrap -->