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
	<?php GoalsHelperFE::showDashHeader('active'); ?>
 <div class="gl_goals">
     <?php include_once('default_goals.php'); ?>
 </div>
 </div>
</div><!-- end #goals-wrap -->