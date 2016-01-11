<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class GoalsTableHabitlog extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__goals_habits_log', 'id', $db);
	}
	
	function store($updateNulls = false)
	{
		return parent::store();
	}
}