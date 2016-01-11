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

class GoalsModelStages extends JModelList
{
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

	    $pid = JRequest::getInt('pid',0);
	    $user = JFactory::getUser();

		$query->select('m.*');
		$query->select('`g`.`title` AS `gtitle`');
		$query->join('LEFT','`#__goals_plans` AS `g` ON `g`.`id`=`m`.`pid`');
		$query->from('`#__goals_stages` AS `m`');
		if ($pid) $query->where('`m`.`pid`='.(int)$pid);
		$query->where('`g`.`uid`='.$user->id);
		$query->order('`m`.`duedate` DESC');
		return $query;
	}
}