<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/userfield.php';

class GoalsModelEditfield extends GoalsModelUserfield
{
	public function getTable($type = 'userfield', $prefix = 'GoalsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}