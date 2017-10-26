<?php

/**

* Goals component for Joomla 3.0

* @package Goals

* @author JoomPlace Team

* @Copyright Copyright (C) JoomPlace, www.joomplace.com

* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html

**/

defined('_JEXEC') or die('Restricted access');

?>



 <div class="gl_dashboard">

 <?php GoalsHelperFE::showDashHeader('COM_MY_GOALS',1); ?>



<form action="<?php echo JRoute::_('index.php?option=com_goals&view=goals'); ?>" method="post" name="adminForm" >

 <div class="gl_goals">

 		<?php echo $this->loadTemplate('modalgoals'); ?>

 		<div class="pagination">

		<?php echo $this->pagination->getPagesLinks(); ?>

		</div>

 </div>

 	<input type="hidden" name="task" value="" />

 	<input type="hidden" name="tmpl" value="component" />

	<input type="hidden" name="layout" value="modal" />

</form>

 </div>

<div class="clr"></div>