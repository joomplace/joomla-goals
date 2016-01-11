<?php
/**
 * Goals component for Joomla 3.0
 * @package Goals
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die;

include_once(JPATH_SITE.'/components/com_goals/helpers/route.php');

function GoalsBuildRoute( &$query )
{
    return GoalsHelperRoute::buildRoute($query);
}


function GoalsParseRoute( $segments )
{
    return GoalsHelperRoute::parseRoute($segments);
}