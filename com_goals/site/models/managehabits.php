<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class GoalsModelManageHabits extends JModelList
{
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user = JFactory::getUser();

		$query->select('h.*');
		$query->from('`#__goals_habits` AS `h`');
		$query->select('COUNT(l.id) AS `log_count`');
		$query->join('LEFT','`#__goals_habits_log` AS `l` ON l.`hid`=h.`id`');
		$query->where('`h`.`uid`='.$user->id);
		$query->group('`h`.`id`');

		return $query;
	}
}