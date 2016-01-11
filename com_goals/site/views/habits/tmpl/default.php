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
 <div class="gl_dashboard">
	<?php GoalsHelper::showHabitHeader(); ?>
 <div class="gl_goals">
<?php $items = $this->habits; ?>
 		<?php echo $this->loadTemplate('habits'); ?>
 		<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
 </div>

</div>

<div class="clr"></div>
</div><!-- end #goals-wrap -->