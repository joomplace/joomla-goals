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

class GoalsModelMilistones extends JModelList
{
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

	    $gid = JRequest::getInt('gid',0);
	    $user = JFactory::getUser();

		$query->select('m.*');
		$query->select('`g`.`title` AS `gtitle`');
		$query->join('LEFT','`#__goals` AS `g` ON `g`.`id`=`m`.`gid`');
		$query->from('`#__goals_milistones` AS `m`');
		if ($gid) $query->where('`m`.`gid`='.(int)$gid);
		$query->where('`g`.`uid`='.$user->id);
		$query->order('`m`.`duedate` DESC');
		return $query;
	}
}