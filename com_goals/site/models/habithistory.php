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

class GoalsModelhabithistory extends JModelList
{
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState();

		$settings = GoalsHelper::getSettings();

		// Filter on month, year
		$this->setState('filter.cal_start', JRequest::getVar('cal_start'));
		$this->setState('filter.cal_end', JRequest::getVar('cal_end'));

		// Get list limit
		$app = JFactory::getApplication();
		$itemid = JRequest::getInt('Itemid', 0);

	}

	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user = JFactory::getUser();

		$id = JRequest::getInt('id',0);
		$query->select('l.*');
		$query->from('`#__goals_habits_log` AS `l`');
		$query->select('h.`title`,h.`type`');
		$query->join('LEFT','`#__goals_habits` AS `h` ON l.`hid`=h.`id`');
		if ($id) $query->where('`l`.`hid`='.$id);
		if ($cal_start = $this->getState('filter.cal_start')) {
			$query->where('l.`date`>='.$db->quote($cal_start));
		}
		if ($cal_end = $this->getState('filter.cal_end')) {
			$query->where('l.`date`<='.$db->quote($cal_end));
		}
		$query->where('`h`.`uid`='.$user->id);
		$query->order('`l`.`date` DESC');

		return $query;
	}
}