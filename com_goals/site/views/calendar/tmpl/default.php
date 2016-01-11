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
<div id="goals-wrap">
 <div class="gl_dashboard">
	<?php GoalsHelper::showDashHeader();
	?>
	<div id="main">
		<?php  echo $this->loadTemplate('cal'); ?>
	</div>
</div>
<div class="clr"></div>
</div><!-- end #goals-wrap -->