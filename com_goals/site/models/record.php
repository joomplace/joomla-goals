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

class GoalsModelRecord extends JModelList
{
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	    $user = JFactory::getUser();

	    $gid = JRequest::getInt('gid',0);
	    $id = JRequest::getInt('id',0);

		$query->select('r.*');
		$query->select('`g`.`title` AS `gtitle`, g.`metric` AS `gmetric`, `g`.`cid` AS `cid`');
		$query->join('LEFT','`#__goals` AS `g` ON `g`.`id`=`r`.`gid`');
		$query->from('`#__goals_tasks` AS `r`');
		if ($gid) $query->where('`r`.`gid`='.(int)$gid);
		if ($id) $query->where('`r`.`id`='.(int)$id);
		$query->where('`g`.`uid`='.$user->id);

		return $query;
	}
}