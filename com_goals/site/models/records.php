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

class GoalsModelRecords extends JModelList
{
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	    $user = JFactory::getUser();
	    $gid = JRequest::getInt('gid',0);
		$query->select('r.*');
		$query->select('`g`.`title` AS `gtitle`, g.`metric` AS `gmetric`, `g`.`cid` AS `cid`, `g`.`uid` AS `uid`');
		$query->join('LEFT','`#__goals` AS `g` ON `g`.`id`=`r`.`gid`');
		$query->from('`#__goals_tasks` AS `r`');
		$query->where('`g`.`uid`='.$user->id);
		if ($gid) $query->where('`r`.`gid`='.(int)$gid);
		$query->order('`r`.`date` DESC');

		return $query;
	}
}